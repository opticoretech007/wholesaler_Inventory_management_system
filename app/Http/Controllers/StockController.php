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
        $powers = Power::all();

        return view('stock-in', compact('products', 'powers'));
    }

    // STORE STOCK
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'power_id' => 'required',
            'quantity' => 'required|integer'
        ]);

        // check if stock already exists
        $stock = Stock::where('product_id', $request->product_id)
            ->where('power_id', $request->power_id)
            ->first();

        if ($stock) {
            $stock->quantity += $request->quantity;
            $stock->save();
        } else {
            Stock::create([
                'product_id' => $request->product_id,
                'power_id' => $request->power_id,
                'quantity' => $request->quantity
            ]);
        }

        \App\Models\StockTransaction::create([
            'product_id' => $request->product_id,
            'power_id' => $request->power_id,
            'type' => 'IN',
            'quantity' => $request->quantity
        ]);


        return back()->with('success', 'Stock Added Successfully!');
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