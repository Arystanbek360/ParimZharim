<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\LoyaltyProgram\Adapters\Api\ApiController\LoyaltyProgramCustomerController;
use Modules\ParimZharim\Profile\Adapters\Api\ApiControllers\CustomerApiController;

Route::prefix('api/loyalty')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/get-customer-discount', [LoyaltyProgramCustomerController::class, 'getCurrentAndNextCustomerDiscount']);
    });

});
