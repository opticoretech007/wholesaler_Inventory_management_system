<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Power;
use App\Models\Category;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->latest();

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->filled('date')) {
            $query->whereDate('invoice_date', $request->date);
        }

        $purchases = $query->paginate(20)->withQueryString();
        $totalPurchases = Purchase::sum('net_total');

        return view('purchases.index', compact('purchases', 'totalPurchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products  = Product::all();
        $powers    = Power::orderBy('sph')->get();
        $categories = Category::all();

        // Auto-generate invoice number
        $lastPurchase = Purchase::latest()->first();
        $invoiceNo = 'PUR-' . str_pad(($lastPurchase ? $lastPurchase->id + 1 : 1), 5, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'powers', 'categories', 'invoiceNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'  => 'required|exists:suppliers,id',
            'invoice_date' => 'required|date',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.power_id'   => 'required|exists:powers,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate totals
        $grossTotal = 0;
        foreach ($request->items as $item) {
            $grossTotal += $item['quantity'] * $item['unit_price'];
        }

        $discount = $request->discount ?? 0;
        $tax      = $request->tax ?? 0;
        $netTotal = $grossTotal - $discount + $tax;
        $paid     = $request->paid ?? 0;
        $balance  = $netTotal - $paid;

        // Create purchase
        $purchase = Purchase::create([
            'invoice_no'   => $request->invoice_no,
            'supplier_id'  => $request->supplier_id,
            'invoice_date' => $request->invoice_date,
            'gross_total'  => $grossTotal,
            'discount'     => $discount,
            'tax'          => $tax,
            'net_total'    => $netTotal,
            'paid'         => $paid,
            'balance'      => $balance,
            'payment_mode' => $request->payment_mode ?? 'cash',
            'memo'         => $request->memo,
        ]);

        // Save items + update stock
        foreach ($request->items as $item) {
            $totalPrice = $item['quantity'] * $item['unit_price'];

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $item['product_id'],
                'power_id'    => $item['power_id'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total_price' => $totalPrice,
                'discount'    => $item['discount'] ?? 0,
            ]);

            // Update stock
            $stock = Stock::where('product_id', $item['product_id'])
                          ->where('power_id', $item['power_id'])
                          ->first();

            if ($stock) {
                $stock->quantity += $item['quantity'];
                $stock->save();
            } else {
                Stock::create([
                    'product_id' => $item['product_id'],
                    'power_id'   => $item['power_id'],
                    'quantity'   => $item['quantity'],
                ]);
            }

            // Stock transaction log
            StockTransaction::create([
                'product_id' => $item['product_id'],
                'power_id'   => $item['power_id'],
                'type'       => 'IN',
                'quantity'   => $item['quantity'],
            ]);
        }

        // Update supplier balance
        $supplier = Supplier::find($request->supplier_id);
        $supplier->current_balance += $balance;
        $supplier->save();

        return redirect('/purchases')->with('success', '✅ Purchase saved successfully!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product', 'items.power');
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        // Reverse stock
        foreach ($purchase->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                          ->where('power_id', $item->power_id)
                          ->first();
            if ($stock) {
                $stock->quantity -= $item->quantity;
                $stock->save();
            }
        }

        // Reverse supplier balance
        $purchase->supplier->current_balance -= $purchase->balance;
        $purchase->supplier->save();

        $purchase->items()->delete();
        $purchase->delete();

        return redirect('/purchases')->with('success', '🗑️ Purchase deleted.');
    }

    public function edit(Purchase $purchase) { return redirect('/purchases'); }
    public function update(Request $request, Purchase $purchase) { return redirect('/purchases'); }

    public function payBalance(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01|max:' . $purchase->balance,
            'payment_mode' => 'required|in:cash,bank_transfer',
            'notes'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // Record the payment
            PurchasePayment::create([
                'purchase_id'  => $purchase->id,
                'amount'       => $request->amount,
                'payment_mode' => $request->payment_mode,
                'notes'        => $request->notes,
                'paid_by'      => auth()->id(),
            ]);

            // Update purchase paid/balance
            $purchase->paid += $request->amount;
            $purchase->balance -= $request->amount;
            $purchase->save();

            // Update supplier balance (what we owe them goes down)
            $supplier = $purchase->supplier;
            if ($supplier) {
                $supplier->current_balance -= $request->amount;
                $supplier->save();
            }
        });

        return redirect("/purchases/{$purchase->id}")
            ->with('success', '💰 Payment recorded successfully.');
    }
}