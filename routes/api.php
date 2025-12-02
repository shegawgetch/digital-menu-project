<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\QRCodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public and protected routes are separated. Sanctum middleware is applied
| only to routes that require authentication. Public routes remain accessible
| without authentication.
|
*/

// ----------------- PUBLIC ROUTES -----------------

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'Test Work',
    ]);
});

// Authentication routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/show', [AuthController::class, 'show']);

// Public menu routes
Route::get('/menu/{userId}', [PublicMenuController::class, 'show']);
//Route::get('/menu/{userId}/qrcode', [PublicMenuController::class, 'generateQRCode']);

// Public menu items
Route::get('/menu-items', [MenuItemController::class, 'index']);

// Place order public for customers
Route::post('/orders', [OrderController::class, 'store']);

// ----------------- PROTECTED ROUTES (auth:sanctum) -----------------
Route::middleware('auth:sanctum')->group(function () {

    // Categories
    Route::apiResource('categories', CategoryController::class);
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDestroy']);


    // Menu items
    Route::apiResource('menu-items', MenuItemController::class);
 Route::apiResource('menu-items', MenuItemController::class);
Route::post('/menu-items/{id}/restore', [MenuItemController::class, 'restore']);
    Route::delete('/menu-items/{id}/force', [MenuItemController::class, 'forceDestroy']);



     
    Route::middleware('auth:sanctum')->get('/generate-qr', [QRCodeController::class, 'generate']);

    // Orders
    Route::apiResource('orders', OrderController::class)->except(['store']); // store is public
    Route::get('/orders', [OrderController::class, 'getOrdersForLoggedInUser']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

    // Authenticated user routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});
