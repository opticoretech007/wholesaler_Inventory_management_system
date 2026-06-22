<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Power;
use App\Models\StockTransaction;

class StockEditController extends Controller
{
    // List all stocks (search/select karne ke liye)
    public function index(Request $request)
    {
        $query = Stock::with(['product', 'power']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('power', function ($q) use ($search) {
                $q->where('sph', 'like', "%$search%")
                  ->orWhere('cyl', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            })->orWhereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $stocks = $query->orderBy('product_id')->paginate(30)->withQueryString();

        return view('stock-edit-list', compact('stocks'));
    }

    // Show edit form
    public function edit($id)
    {
        $stock = Stock::with(['product', 'power'])->findOrFail($id);
        return view('stock-edit-form', compact('stock'));
    }

    // Update stock manually
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'reason'   => 'required|string|max:255',
        ]);

        $stock = Stock::findOrFail($id);
        $oldQuantity = $stock->quantity;
        $newQuantity = $request->quantity;
        $difference = $newQuantity - $oldQuantity;

        $stock->quantity = $newQuantity;
        $stock->save();

        // Log this correction as a transaction
        if ($difference != 0) {
            StockTransaction::create([
                'product_id' => $stock->product_id,
                'power_id'   => $stock->power_id,
                'type'       => $difference > 0 ? 'IN' : 'OUT',
                'quantity'   => abs($difference),
            ]);
        }

        return redirect('/stock-edit')->with('success',
            "✅ Stock updated: {$oldQuantity} → {$newQuantity} (Reason: {$request->reason})");
    }
}