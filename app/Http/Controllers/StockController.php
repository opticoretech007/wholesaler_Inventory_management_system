<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Power;
use App\Models\Stock;

class StockController extends Controller
{
    // SHOW FORM
    public function create()
    {
        $products = Product::all();
        $powers = Power::with('subclass.lensClass.category')->orderBy('sph')->get();
        $categories = \App\Models\Category::with('classes.subclasses')->get();

        return view('stock-in', compact('products', 'powers', 'categories'));
    }

    // STORE STOCK
    public function store(Request $request)
{
    $request->validate([
        'product_id'   => 'required',
        'power_ids'    => 'required|array|min:1',
        'power_ids.*'  => 'exists:powers,id',
        'quantity'     => 'required|integer|min:1',
        'lp_price'        => 'nullable|numeric',
        'retail_price'    => 'nullable|numeric',
        'wholesale_price' => 'nullable|numeric',
        //'company_price'   => 'nullable|numeric',
    ]);

    $count = 0;

    foreach ($request->power_ids as $powerId) {
        $stock = Stock::where('product_id', $request->product_id)
                      ->where('power_id', $powerId)
                      ->first();

        if ($stock) {
            $stock->quantity         += $request->quantity;
            $stock->lp_price          = $request->lp_price ?? $stock->lp_price;
            $stock->retail_price      = $request->retail_price ?? $stock->retail_price;
            $stock->wholesale_price   = $request->wholesale_price ?? $stock->wholesale_price;
            //$stock->company_price     = $request->company_price ?? $stock->company_price;
            $stock->save();
        } else {
            Stock::create([
                'product_id'      => $request->product_id,
                'power_id'        => $powerId,
                'quantity'        => $request->quantity,
                'lp_price'        => $request->lp_price ?? 0,
                'retail_price'    => $request->retail_price ?? 0,
                'wholesale_price' => $request->wholesale_price ?? 0,
                //'company_price'   => $request->company_price ?? 0,
            ]);
        }

        \App\Models\StockTransaction::create([
            'product_id' => $request->product_id,
            'power_id'   => $powerId,
            'type'       => 'IN',
            'quantity'   => $request->quantity,
        ]);

        $count++;
    }

    return back()->with('success', "✅ Stock added for $count powers successfully!");
}

    // SHOW STOCK OUT FORM
    public function stockOutForm()
    {
        $products = Product::all();
        $powers = Power::all();
        return view('stock-out', compact('products', 'powers'));
    }

    // PROCESS STOCK OUT
    public function stockOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'power_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $stock = Stock::where('product_id', $request->product_id)
            ->where('power_id', $request->power_id)
            ->first();

        // No stock record exists
        if (!$stock) {
            return back()->with('error', 'No stock found for this product and power!');
        }

        // Insufficient stock
        if ($stock->quantity < $request->quantity) {
            return back()->with('error', 'Insufficient Stock! Available: ' . $stock->quantity);
        }

        // Deduct stock
        $stock->quantity -= $request->quantity;
        $stock->save();

        // Save transaction
        \App\Models\StockTransaction::create([
            'product_id' => $request->product_id,
            'power_id' => $request->power_id,
            'type' => 'OUT',
            'quantity' => $request->quantity
        ]);

        return back()->with('success', 'Stock OUT recorded successfully!');
    }
}