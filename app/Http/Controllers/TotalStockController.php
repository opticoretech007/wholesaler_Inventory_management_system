<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Power;

class TotalStockController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'list'); // list or grid

        $products = Product::all();
        $categories = Power::select('category')->distinct()->whereNotNull('category')->pluck('category');

        // Base query: only stocks with quantity > 0
        $query = Stock::with(['product', 'power.subclass.lensClass.category'])->where('quantity', '>', 0);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('category')) {
            $query->whereHas('power', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $stocks = $query->get();

        $totalQuantity = $stocks->sum('quantity');
        $totalItems = $stocks->count();

        // Build grid data if needed
        $grid = [];
        if ($view === 'grid') {
            $powersWithStock = $stocks->pluck('power')->unique('id')->sortBy('sph');

            foreach ($powersWithStock as $power) {
                $grid[$power->id] = [];
                foreach ($products as $product) {
                    $stock = $stocks->first(function ($s) use ($power, $product) {
                        return $s->power_id == $power->id && $s->product_id == $product->id;
                    });
                    $grid[$power->id][$product->id] = $stock ? $stock->quantity : 0;
                }
            }

            return view('total-stock', compact(
                'stocks', 'products', 'categories', 'view',
                'totalQuantity', 'totalItems', 'grid', 'powersWithStock'
            ));
        }

        return view('total-stock', compact(
            'stocks', 'products', 'categories', 'view',
            'totalQuantity', 'totalItems'
        ));
    }
}