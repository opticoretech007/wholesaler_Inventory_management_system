<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function index()
    {
        $query = Sale::with('customer', 'salesman')->latest();

        if (request('q')) {
            $q = request('q');
            $query->where(function($r) use ($q) {
                $r->where('invoice_no', 'like', "%{$q}%")
                  ->orWhereHas('customer', function($c) use ($q) {
                      $c->where('name', 'like', "%{$q}%");
                  });
            });
        }

        if (request('date_from')) {
            $query->whereDate('invoice_date', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('invoice_date', '<=', request('date_to'));
        }

        $sales = $query->paginate(20)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products  = \App\Models\Product::orderBy('name')->get();
        $powers    = \App\Models\Power::orderBy('sph')->get();
        $salesmen  = User::orderBy('name')->get();
        return view('sales.create', compact('customers', 'products', 'powers', 'salesmen'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'salesman_id'          => 'nullable|exists:users,id',
            'invoice_date'         => 'required|date',
            'due_date'             => 'nullable|date',
            'price_type'           => 'nullable|in:retail,wholesale,company',
            'memo'                 => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.power_id'     => 'nullable|exists:powers,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.discount'     => 'nullable|numeric|min:0',
            'discount'             => 'nullable|numeric|min:0',
            'tax'                  => 'nullable|numeric|min:0',
            'paid'                 => 'nullable|numeric|min:0',
            'payment_mode'         => 'nullable|in:cash,credit,bank',
        ]);

        $gross = 0;
        foreach ($data['items'] as $it) {
            $gross += $it['quantity'] * $it['unit_price'];
        }

        $discount = $data['discount'] ?? 0;
        $tax      = $data['tax']      ?? 0;
        $net      = $gross - $discount + $tax;
        $paid     = $data['paid']     ?? 0;
        $balance  = $net - $paid;

        Log::info('Sales.store attempt', [
            'customer_id' => $data['customer_id'],
            'gross' => $gross, 'net' => $net, 'paid' => $paid
        ]);

        try {
            DB::beginTransaction();

            $invoiceNo = 'S' . date('Ymd') . strtoupper(Str::random(4));

            $sale = Sale::create([
                'invoice_no'   => $invoiceNo,
                'customer_id'  => $data['customer_id'],
                'salesman_id'  => $data['salesman_id'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date'     => $data['due_date'] ?? null,
                'price_type'   => $data['price_type'] ?? 'wholesale',
                'memo'         => $data['memo'] ?? null,
                'gross_total'  => $gross,
                'discount'     => $discount,
                'tax'          => $tax,
                'net_total'    => $net,
                'paid'         => $paid,
                'balance'      => $balance,
                'payment_mode' => $data['payment_mode'] ?? 'cash',
            ]);

            foreach ($data['items'] as $it) {
                $stock = Stock::firstOrCreate([
                    'product_id' => $it['product_id'],
                    'power_id'   => $it['power_id'] ?? null,
                ], ['quantity' => 0]);

                if ($stock->quantity < $it['quantity']) {
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        "Insufficient stock for product ID {$it['product_id']} (power: " . ($it['power_id'] ?? 'none') . ")"
                    ]);
                }

                $sale->items()->save(new SaleItem([
                    'product_id'  => $it['product_id'],
                    'power_id'    => $it['power_id'] ?? null,
                    'quantity'    => $it['quantity'],
                    'unit_price'  => $it['unit_price'],
                    'total_price' => $it['quantity'] * $it['unit_price'],
                    'discount'    => $it['discount'] ?? 0,
                ]));

                $stock->quantity -= $it['quantity'];
                $stock->save();

                StockTransaction::create([
                    'product_id' => $it['product_id'],
                    'power_id'   => $it['power_id'] ?? null,
                    'type'       => 'OUT',
                    'quantity'   => $it['quantity'],
                ]);
            }

            if ($paid > 0) {
                \App\Models\Payment::create([
                    'payable_type' => Sale::class,
                    'payable_id'   => $sale->id,
                    'amount'       => $paid,
                    'method'       => $data['payment_mode'] ?? 'cash',
                    'date'         => $data['invoice_date'],
                ]);

                $customer = Customer::find($data['customer_id']);
                if ($customer) {
                    $customer->current_balance = ($customer->current_balance ?? 0) - $paid;
                    $customer->save();
                }
            }

            Log::info('Sale created', ['sale_id' => $sale->id, 'invoice_no' => $sale->invoice_no]);
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale create failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create sale: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $sale = Sale::with('items.product', 'items.power', 'customer', 'salesman')->findOrFail($id);
        return view('sales.invoice', compact('sale'));
    }

    public function edit($id)
    {
        $sale      = Sale::with('items')->findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $salesmen  = User::orderBy('name')->get();
        return view('sales.edit', compact('sale', 'customers', 'salesmen'));
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);
        $data = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'salesman_id'  => 'nullable|exists:users,id',
            'invoice_date' => 'required|date',
            'due_date'     => 'nullable|date',
            'price_type'   => 'nullable|in:retail,wholesale,company',
            'memo'         => 'nullable|string|max:500',
            'discount'     => 'nullable|numeric|min:0',
            'tax'          => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|in:cash,credit,bank',
        ]);

        $sale->update($data);
        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully!');
    }

    public function pdf($id)
    {
        $sale = Sale::with('items.product', 'items.power', 'customer', 'salesman', 'payments')->findOrFail($id);
        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.invoice_pdf', compact('sale'));
        return $pdf->stream("invoice-{$sale->invoice_no}.pdf");
    }
}