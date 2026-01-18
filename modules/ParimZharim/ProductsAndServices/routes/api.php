<?php declare(strict_types=1);

use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\ApiControllers\ProductAndServiceApiController;

Route::prefix('api/products-services')->middleware(HandleCors::class)->group(function () {
    Route::get('/get-product-categories', [ProductAndServiceApiController::class, 'getProductCategories'])->name('api.products-services.get-product-categories');
    Route::get('/get-products-by-category', [ProductAndServiceApiController::class, 'getProductsByCategory'])->name('api.products-services.get-products-by-category');
    Route::get('/get-service-categories', [ProductAndServiceApiController::class, 'getServiceCategories'])->name('api.products-services.get-service-categories');
    Route::get('/get-services-by-category', [ProductAndServiceApiController::class, 'getServicesByCategory'])->name('api.products-services.get-services-by-category');
    Route::get('/get-all-products-grouped-by-category', [ProductAndServiceApiController::class, 'getAllProductsGroupedByCategory'])->name('api.products-services.get-all-products-grouped-by-category');
});
