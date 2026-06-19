<?php

use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockGridController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index']);   // 👈 ye andar honi chahiye
    // baaki saari routes bhi (stock-in, stock-out, reports, transactions, stock-grid) yahan andar
});
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/pdf/stock', [ReportController::class, 'stockPdf']);
Route::get('/reports/pdf/transactions', [ReportController::class, 'transactionsPdf']);

Route::get('/transactions', [TransactionController::class, 'index']);

Route::get('/stock-grid', [StockGridController::class, 'index']);

Route::get('/stock-in', [StockController::class, 'create']);
Route::post('/stock-in', [StockController::class, 'store']);

Route::get('/stock-out', [StockController::class, 'stockOutForm']);
Route::post('/stock-out', [StockController::class, 'stockOut']);