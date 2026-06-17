<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        // Total stock across all products
        $totalStock = Stock::sum('quantity');

        // Low stock items (quantity less than 5)
        $lowStock = Stock::with(['product', 'power'])
                        ->where('quantity', '<', 5)
                        ->get();

        // Recent 10 transactions
        $recentTransactions = StockTransaction::with(['product', 'power'])
                                ->latest()
                                ->take(10)
                                ->get();

        return view('dashboard', compact('totalStock', 'lowStock', 'recentTransactions'));
    }
}