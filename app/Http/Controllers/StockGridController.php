<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Power;
use App\Models\Stock;

class StockGridController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();

        $powersQuery = Power::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $powersQuery->where(function ($q) use ($search) {
                $q->where('sph', 'like', "%$search%")
                  ->orWhere('cyl', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        $powers = $powersQuery->orderBy('sph')->get();

        $grid = [];
        foreach ($powers as $power) {
            $grid[$power->id] = [];
            foreach ($products as $product) {
                $stock = Stock::where('product_id', $product->id)
                              ->where('power_id', $power->id)
                              ->first();
                $grid[$power->id][$product->id] = $stock ? $stock->quantity : 0;
            }
        }

        return view('stock-grid', compact('products', 'powers', 'grid'));
    }
}