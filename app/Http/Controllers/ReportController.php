<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $products = Product::all();

        $stockSummary = Stock::with(['product', 'power'])
                            ->orderBy('product_id')
                            ->get();

        $totalStock = Stock::sum('quantity');

        $lowStock = Stock::with(['product', 'power'])
                        ->where('quantity', '<', 5)
                        ->get();

        $recentTransactions = StockTransaction::with(['product', 'power'])
                                ->latest()
                                ->take(20)
                                ->get();

        return view('reports', compact(
            'products', 'stockSummary', 'totalStock',
            'lowStock', 'recentTransactions'
        ));
    }

    public function stockPdf()
    {
        $stockSummary = Stock::with(['product', 'power'])
                            ->orderBy('product_id')
                            ->get();

        $totalStock = Stock::sum('quantity');

        $pdf = Pdf::loadView('pdf.stock-report', compact('stockSummary', 'totalStock'));

        return $pdf->download('stock-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function transactionsPdf()
    {
        $transactions = StockTransaction::with(['product', 'power'])
                            ->latest()
                            ->get();

        $pdf = Pdf::loadView('pdf.transactions-report', compact('transactions'));

        return $pdf->download('transactions-report-' . now()->format('Y-m-d') . '.pdf');
    }
}