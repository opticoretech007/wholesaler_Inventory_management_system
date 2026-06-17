<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Power;
use App\Models\Stock;

class StockGridController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $powers = Power::all();

        // Build grid: power_id => [product_id => quantity]
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