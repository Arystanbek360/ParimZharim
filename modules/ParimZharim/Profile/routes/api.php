<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\Profile\Adapters\Api\ApiControllers\CustomerApiController;

Route::prefix('api/profile')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/customer/register', [CustomerApiController::class, 'register']);

        Route::get('/customer/get-profile', [CustomerApiController::class, 'getProfile']);
        Route::post('/customer/update-profile', [CustomerApiController::class, 'update']);
        Route::delete('/customer/delete-profile', [CustomerApiController::class, 'delete']);

        Route::post('/customer/change-phone', [CustomerApiController::class, 'changePhone']);

    });

    Route::post('/customer/get-access-token-by-auth-phone-code', [CustomerApiController::class, 'getAccessTokenByAuthPhoneCode']);
});
