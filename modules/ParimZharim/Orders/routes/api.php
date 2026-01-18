<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\Orders\Adapters\Api\ApiControllers\OrderableObjectApiController;
use Modules\ParimZharim\Orders\Adapters\Api\ApiControllers\OrderApiController;

Route::prefix('api/orders')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/create', [OrderApiController::class, 'createOrderByCustomer']);
        Route::post('/precalculate', [OrderApiController::class, 'preCalculatePriceByDateAndObject']);
        Route::post('/add-order-items', [OrderApiController::class, 'requestUpdatingOrder']);
        Route::get('/cancel', [OrderApiController::class, 'requestOrderCancellation']);
        Route::get('/get-active-order', [OrderApiController::class, 'getActiveOrder']);
        Route::get('/get-orders', [OrderApiController::class, 'getOrdersByCustomer']);
        Route::get('/get-order-by-id', [OrderApiController::class, 'viewOrderDetailsByCustomer']);
        Route::post('/create-payment', [OrderApiController::class, 'createPaymentForOrder']);
    });
});

Route::prefix('api/orderable-service-objects')->group(function () {
    Route::get('/get-by-category-id', [OrderableObjectApiController::class, 'getOrderableServiceObjectsByCategoryID']);
    Route::get('/get', [OrderableObjectApiController::class, 'getOrderableServiceObjectByID']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/get-free-slots-for-object-and-date', [OrderableObjectApiController::class, 'getFreeSlotsForObjectAndDate']);
        Route::get('/get-free-days-for-service-object', [OrderableObjectApiController::class, 'getFreeDaysForServiceObjectForInterval']);
    });
});
