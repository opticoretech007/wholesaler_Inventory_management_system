<?php

use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/stock-in', [StockController::class, 'create']);
Route::post('/stock-in', [StockController::class, 'store']);

Route::get('/stock-out', [StockController::class, 'stockOutForm']);
Route::post('/stock-out', [StockController::class, 'stockOut']);