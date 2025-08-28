<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User-related routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('roles');

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
        ]);
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('web')
        ->name('logout');

    Route::get('/supplier/products', [ProductController::class, 'supplierProducts']);
    Route::get('/supplier/orders', [OrderController::class, 'supplierOrders']);
    Route::get('/user/orders', [OrderController::class, 'userOrders']);
});

// Authentication routes
Route::middleware(['guest'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
});

// Product-related routes
Route::prefix('products')->group(function () {
    // Public routes
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);

    // Protected routes (policies handle authorization)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::patch('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
    });
});

// Order-related routes
Route::prefix('orders')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::patch('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });
});

// Apply rate limiting to all routes
Route::middleware('throttle:60,1')->group(function () {
    // All routes above will inherit this rate limit
});
