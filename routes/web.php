<?php

use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockGridController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/pdf/stock', [ReportController::class, 'stockPdf']);
Route::get('/reports/pdf/transactions', [ReportController::class, 'transactionsPdf']);

Route::get('/transactions', [TransactionController::class, 'index']);

Route::get('/stock-grid', [StockGridController::class, 'index']);
Route::get('/', [DashboardController::class, 'index']);

Route::get('/stock-in', [StockController::class, 'create']);
Route::post('/stock-in', [StockController::class, 'store']);

Route::get('/stock-out', [StockController::class, 'stockOutForm']);
Route::post('/stock-out', [StockController::class, 'stockOut']);