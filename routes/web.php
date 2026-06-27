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
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SalesController;

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Auth protected routes
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);

    // Stock
    Route::get('/stock-in', [StockController::class, 'create']);
    Route::post('/stock-in', [StockController::class, 'store']);
    Route::get('/stock-out', [StockController::class, 'stockOutForm']);
    Route::post('/stock-out', [StockController::class, 'stockOut']);
    Route::get('/stock-grid', [StockGridController::class, 'index']);
    Route::get('/total-stock', [TotalStockController::class, 'index']);
    Route::get('/stock-edit', [StockEditController::class, 'index']);
    Route::get('/stock-edit/{id}', [StockEditController::class, 'edit']);
    Route::put('/stock-edit/{id}', [StockEditController::class, 'update']);

    // Powers
    // API for dynamic dropdowns
    Route::get('/api/classes/{categoryId}', [PowerGeneratorController::class, 'getClasses']);
    Route::get('/api/subclasses/{classId}', [PowerGeneratorController::class, 'getSubclasses']);
    Route::get('/powers/generate', [PowerGeneratorController::class, 'form']);
    Route::post('/powers/generate', [PowerGeneratorController::class, 'generate']);
    Route::get('/powers', [PowerGeneratorController::class, 'index']);
    Route::delete('/powers/delete-all', [PowerGeneratorController::class, 'destroyAll']);
    Route::delete('/powers/category/{category}', [PowerGeneratorController::class, 'destroyCategory']);
    Route::delete('/powers/{id}', [PowerGeneratorController::class, 'destroy']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/pdf/stock', [ReportController::class, 'stockPdf']);
    Route::get('/reports/pdf/transactions', [ReportController::class, 'transactionsPdf']);

    // Customers & Suppliers
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/{purchase}/return', [PurchaseReturnController::class, 'create']);
    Route::post('/purchases/{purchase}/return', [PurchaseReturnController::class, 'store']);

    // Sales
    Route::resource('sales', SalesController::class);
    Route::get('/sales/{id}/pdf', [SalesController::class, 'pdf'])->name('sales.pdf');

    Route::get('/api/powers/{subclassId}', [PowerGeneratorController::class, 'getPowers']);

    
    Route::post('/purchases/{purchase}/pay', [PurchaseController::class, 'payBalance']);
});