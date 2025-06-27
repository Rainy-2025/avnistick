<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ServiceController;
// Public login route
Route::post('/admin/login', action: [AdminAuthController::class, 'login']);

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

    // Contact CRUD
      Route::get('/admin/contacts', [ContactController::class, 'index']);
    Route::put('/admin/contacts/{id}', [ContactController::class, 'update']);
    Route::delete('/admin/contacts/{id}', [ContactController::class, 'destroy']);


      // Services
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

    // Subservices
    Route::post('/services/{id}/subservices', [ServiceController::class, 'addSubservice']);
    Route::put('/subservices/{id}', [ServiceController::class, 'updateSubservice']);
    Route::delete('/subservices/{id}', [ServiceController::class, 'deleteSubservice']);
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

// home page Routes 
Route::get('/user/categories', [CategoryController::class, 'getUserCategories']);

Route::get('/user/products_eight', [ProductController::class, 'latestEightProducts']);


//navbar categories

Route::get('/user/navbar_categories', [CategoryController::class, 'getNavbarCategories']);
Route::get('/user/categories/{id}/subcategories', [CategoryController::class, 'getSubcategoriesByCategory']);

//  collection api sub categories and products
Route::get('/user/subcategories-with-products', [ProductController::class, 'allSubcategoriesWithProducts']);

//contact form api
Route::post('/contact', [ContactController::class, 'submit']);


// Frontend - Services & Subservices View
Route::get('/user/services', [ServiceController::class, 'getServicesWithSubservices']);
Route::get('/user/services/{id}/subservices', [ServiceController::class, 'getSubservicesByService']);
// Filtered product listing
Route::get('/user/products/filter', [ProductController::class, 'filterProducts']);

Route::get('/user/search', [ProductController::class, 'searchAll']);
