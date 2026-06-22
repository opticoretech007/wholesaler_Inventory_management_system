<?php

use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockGridController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PowerGeneratorController;
use App\Http\Controllers\TotalStockController;
use App\Http\Controllers\StockEditController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;

Route::resource('purchases', PurchaseController::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index']);   // 👈 ye andar honi chahiye
    // baaki saari routes bhi (stock-in, stock-out, reports, transactions, stock-grid) yahan andar

    Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/pdf/stock', [ReportController::class, 'stockPdf']);
Route::get('/reports/pdf/transactions', [ReportController::class, 'transactionsPdf']);

Route::get('/transactions', [TransactionController::class, 'index']);

Route::get('/stock-grid', [StockGridController::class, 'index']);

Route::get('/stock-in', [StockController::class, 'create']);
Route::post('/stock-in', [StockController::class, 'store']);

Route::get('/stock-out', [StockController::class, 'stockOutForm']);
Route::post('/stock-out', [StockController::class, 'stockOut']);
});

Route::get('/powers/generate', [PowerGeneratorController::class, 'form']);
Route::post('/powers/generate', [PowerGeneratorController::class, 'generate']);
Route::get('/powers', [PowerGeneratorController::class, 'index']);
Route::delete('/powers/{id}', [PowerGeneratorController::class, 'destroy']);
Route::delete('/powers/category/{category}', [PowerGeneratorController::class, 'destroyCategory']);

Route::get('/total-stock', [TotalStockController::class, 'index']);

Route::get('/stock-edit', [StockEditController::class, 'index']);
Route::get('/stock-edit/{id}', [StockEditController::class, 'edit']);
Route::put('/stock-edit/{id}', [StockEditController::class, 'update']);

Route::resource('customers', CustomerController::class);
Route::resource('suppliers', SupplierController::class);