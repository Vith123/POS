<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// PUBLIC ROUTES (No authentication required)
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED ROUTES (Require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // ============================================
    // AUTHENTICATION ROUTES
    // ============================================
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ============================================
    // CATEGORY ROUTES
    // ============================================
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::get('/{id}/products', [CategoryController::class, 'products']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    // ============================================
    // PRODUCT ROUTES
    // ============================================
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/low-stock', [ProductController::class, 'lowStock']);
        Route::get('/out-of-stock', [ProductController::class, 'outOfStock']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{id}', [ProductController::class, 'update']); // POST for file upload
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

    // ============================================
    // INVENTORY ROUTES
    // ============================================
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']);
        Route::get('/movements', [InventoryController::class, 'movements']);
        Route::get('/{productId}', [InventoryController::class, 'show']);
        Route::post('/stock-in', [InventoryController::class, 'stockIn']);
        Route::post('/stock-out', [InventoryController::class, 'stockOut']);
        Route::post('/adjust', [InventoryController::class, 'adjust']);
        Route::put('/{productId}/min-level', [InventoryController::class, 'updateMinStockLevel']);
    });

    // ============================================
    // SALES ROUTES (Phase 4)
    // ============================================
    Route::prefix('sales')->group(function () {
        Route::get('/', [SaleController::class, 'index']);
        Route::get('/today', [SaleController::class, 'todaySummary']);
        Route::get('/statistics', [SaleController::class, 'statistics']);
        Route::get('/{id}', [SaleController::class, 'show']);
        Route::post('/', [SaleController::class, 'store']);
        Route::put('/{id}/status', [SaleController::class, 'updateStatus']);
    });

    // ============================================
    // CUSTOMER ROUTES (Phase 5)
    // ============================================
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/vip', [CustomerController::class, 'vipCustomers']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::get('/{id}/purchases', [CustomerController::class, 'purchaseHistory']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
        Route::post('/{id}/loyalty-points', [CustomerController::class, 'manageLoyaltyPoints']);
    });

    // ============================================
    // RETURN ROUTES (Phase 5)
    // ============================================
    Route::prefix('returns')->group(function () {
        Route::get('/', [ReturnController::class, 'index']);
        Route::get('/pending', [ReturnController::class, 'pending']);
        Route::get('/statistics', [ReturnController::class, 'statistics']);
        Route::get('/{id}', [ReturnController::class, 'show']);
        Route::post('/', [ReturnController::class, 'store']);
        Route::put('/{id}/status', [ReturnController::class, 'updateStatus']);
    });

    // ============================================
    // REPORT ROUTES (Phase 6) - Refactored with Service Layer
    // ============================================
    Route::prefix('reports')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'index']);        // Dashboard overview
        Route::get('/sales', [ReportController::class, 'sales']);            // Sales report
        Route::get('/products', [ReportController::class, 'products']);      // Product performance
        Route::get('/customers', [ReportController::class, 'customers']);    // Customer insights
        Route::get('/inventory', [ReportController::class, 'inventory']);    // Inventory report
        Route::get('/financial', [ReportController::class, 'financial']);    // Financial summary (Admin only)
        Route::get('/comparative', [ReportController::class, 'comparative']); // Comparative report (Admin only)
    });
});
