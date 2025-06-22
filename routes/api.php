<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;



// Public login route
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Protected routes using Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

        Route::get('/admin/profile', [AdminAuthController::class, 'profile']);


    // Category CRUD
Route::post('/categories', [CategoryController::class, 'addCategory']);
Route::get('/categories', [CategoryController::class, 'getCategoriesWithSubcategories']);
Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory']);

// Subcategory CRUD
Route::post('/categories/{id}/subcategories', [CategoryController::class, 'addSubcategory']);
Route::put('/subcategories/{id}', [CategoryController::class, 'updateSubcategory']);
Route::delete('/subcategories/{id}', [CategoryController::class, 'deleteSubcategory']);

//products
   Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});




// user login register  Routes
Route::post('/send-otp', [AuthController::class, 'sendOtp']);        
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);    
Route::post('/login', [AuthController::class, 'login']);             

// user  Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});