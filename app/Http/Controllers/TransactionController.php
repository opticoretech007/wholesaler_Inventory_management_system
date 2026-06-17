<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Power;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['product', 'power'])->latest();

        // Filter by product
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type IN/OUT
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $transactions = $query->paginate(20);
        $products = Product::all();

        return view('transactions', compact('transactions', 'products'));
    }
}