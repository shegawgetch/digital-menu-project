<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\PublicMenuController;


Route::get('/test', function(){
    return response()->json([
        'message' => 'Test Work'
    ]);
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->apiResource('categories', CategoryController::class);
Route::middleware('auth:sanctum')->apiResource('menu-items', MenuItemController::class);
Route::get('/menu-items', [MenuItemController::class, 'index']);


Route::middleware('auth:sanctum')->apiResource('orders', OrderController::class);
Route::get('menu/{userId}', [OrderController::class, 'publicMenu']);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);

//Route::middleware('auth:sanctum')->get('/generate-qr', [QRCodeController::class, 'generate']);

// Public menu access
Route::get('/menu/{userId}/qrcode', [PublicMenuController::class, 'generateQRCode']);
Route::get('/menu/{userId}', [PublicMenuController::class, 'show']);
// Place order
Route::post('/orders', [OrderController::class, 'store']); // customer creates order
Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'getOrdersForLoggedInUser']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

Route::get('/show', [AuthController::class, 'show']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');

