<?php

use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImportController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn () => view('inventory-dashboard'))->name('dashboard');

    Route::prefix('api')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store'])->middleware('role:admin');
        Route::post('/products/bulk-price-update', [ProductController::class, 'bulkPriceUpdate'])->middleware('role:admin');
        Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('role:admin');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('role:admin');

        Route::post('/sales', [SaleController::class, 'store']);
        Route::get('/sales/{sale}', [SaleController::class, 'show']);

        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::post('/customers/{customer}/settle', [CustomerController::class, 'settle']);

        Route::get('/reports', [ReportController::class, 'index'])->middleware('role:admin');

        Route::get('/api-token', [ApiTokenController::class, 'show'])->middleware('role:admin');
        Route::post('/api-token/regenerate', [ApiTokenController::class, 'regenerate'])->middleware('role:admin');
    });
});

Route::prefix('api/external')->middleware('api.token')->group(function () {
    Route::post('/products/import', [ProductImportController::class, 'store']);
});
