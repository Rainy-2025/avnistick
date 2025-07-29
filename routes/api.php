<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminAuthController,
    AuthController,
    CategoryController,
    ProductController,
    ContactController,
    ServiceController,
    GalleryController,
    LandingPageController,
    ReadySemiProductsController,
    CustomizeProductController,
    AllProductController
};

// Admin Auth
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Admin Profile
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);

    // Category & Subcategory
    Route::post('/categories', [CategoryController::class, 'addCategory']);
    Route::get('/categories', [CategoryController::class, 'getCategoriesWithSubcategories']);
    Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory']);
    Route::post('/categories/{id}/subcategories', [CategoryController::class, 'addSubcategory']);
    Route::put('/subcategories/{id}', [CategoryController::class, 'updateSubcategory']);
    Route::delete('/subcategories/{id}', [CategoryController::class, 'deleteSubcategory']);

   
    // Readymade Products
    Route::post('/readymade-products', [ReadySemiProductsController::class, 'store']);
    Route::get('/readymade-products', [ReadySemiProductsController::class, 'index']);
    Route::get('/readymade-products/{id}', [ReadySemiProductsController::class, 'show']);
    Route::put('/readymade-products/{id}', [ReadySemiProductsController::class, 'update']);
    Route::delete('/readymade-products/{id}', [ReadySemiProductsController::class, 'destroy']);

     Route::post('/customize-products', [CustomizeProductController::class, 'store']);
    Route::get('/customize-products', [CustomizeProductController::class, 'adminIndex']);
    Route::get('/customize-products/{id}', [CustomizeProductController::class, 'show']);
    Route::delete('/customize-products/{id}', [CustomizeProductController::class, 'destroy']);
    // Contact
    Route::get('/admin/contacts', [ContactController::class, 'index']);
    Route::put('/admin/contacts/{id}', [ContactController::class, 'update']);
    Route::delete('/admin/contacts/{id}', [ContactController::class, 'destroy']);

    // Services & Subservices
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    Route::post('/services/{id}/subservices', [ServiceController::class, 'addSubservice']);
    Route::put('/subservices/{id}', [ServiceController::class, 'updateSubservice']);
    Route::delete('/subservices/{id}', [ServiceController::class, 'deleteSubservice']);

    // Gallery Management
    Route::post('/upload-instagram', [GalleryController::class, 'uploadInstagramImages']);
    Route::get('/instagram', [GalleryController::class, 'getInstagramImages']);
    Route::delete('/instagram/{id}', [GalleryController::class, 'deleteInstagramImage']);

    Route::post('/upload-art', [GalleryController::class, 'uploadArtSection']);
    Route::get('/art', [GalleryController::class, 'getArtSection']);
    Route::delete('/art/{id}', [GalleryController::class, 'deleteArtSection']);

    Route::post('/upload-gallery', [GalleryController::class, 'uploadGalleryImages']);
    Route::get('/gallery', [GalleryController::class, 'getGalleryImages']);
    Route::delete('/gallery/{id}', [GalleryController::class, 'deleteGalleryImage']);

    Route::post('/upload-artist', [GalleryController::class, 'uploadArtist']);
    Route::get('/artists', [GalleryController::class, 'getArtists']);
    Route::delete('/artist/{id}', [GalleryController::class, 'deleteArtist']);

    // Banner
    Route::post('/admin/banner', [LandingPageController::class, 'addBanner']);
    Route::get('/admin/banner', [LandingPageController::class, 'getBanners']);
    Route::post('/admin/banner/{id}', [LandingPageController::class, 'updateBanner']);
    Route::delete('/admin/banner/{id}', [LandingPageController::class, 'deleteBanner']);

    // Brand
    Route::post('/admin/brand', [LandingPageController::class, 'addBrand']);
    Route::get('/admin/brand', [LandingPageController::class, 'getBrand']);
    Route::post('/admin/brand/{id}', [LandingPageController::class, 'updateBrand']);
});

// User Auth
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Public User Routes
Route::prefix('user')->group(function () {
    Route::get('/categories', [CategoryController::class, 'getUserCategories']);
    Route::get('/navbar_categories', [CategoryController::class, 'getNavbarCategories']);
    Route::get('/categories/{id}/subcategories', [CategoryController::class, 'getSubcategoriesByCategory']);

    Route::get('/products_eight', [ProductController::class, 'latestEightProducts']);
    Route::get('/products/filter', [ProductController::class, 'filterProducts']);
    Route::get('/search', [ProductController::class, 'searchAll']);
    Route::get('/subcategories-with-products', [ProductController::class, 'allSubcategoriesWithProducts']);

    Route::post('/contact', [ContactController::class, 'submit']);

    Route::get('/services', [ServiceController::class, 'getServicesWithSubservices']);
    Route::get('/services/{id}/subservices', [ServiceController::class, 'getSubservicesByService']);

    Route::get('/instagram-images', [GalleryController::class, 'getInstagramImages']);
    Route::get('/art-section', [GalleryController::class, 'getArtSection']);
    Route::get('/gallery-images', [GalleryController::class, 'getGalleryImages']);
    Route::get('/artists', [GalleryController::class, 'getArtists']);

    Route::get('/readymade-products', action: [ReadySemiProductsController::class, 'userProducts']);
Route::get('/customize-products', [CustomizeProductController::class, 'index']);

     Route::get('/banners', [LandingPageController::class, 'getBanners']);
    Route::get('/brand', [LandingPageController::class, 'getBrand']);
});


Route::get('/all-products', [AllProductController::class, 'index']);
