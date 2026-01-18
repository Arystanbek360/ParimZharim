<?php declare(strict_types=1);

use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;

Route::prefix('api/idm')->middleware(HandleCors::class)->namespace('Modules\Shared\IdentityAndAccessManagement\Adapters\Api\ApiControllers')->group(function() {

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::post('request-phone-change-phone-code', 'ProfileApiController@requestPhoneChangePhoneCode');
        Route::post('change-phone', 'ProfileApiController@changePhone');
        Route::get('get-profile', 'ProfileApiController@getProfile');
        Route::post('update-profile', 'ProfileApiController@updateProfile');

        Route::post('logout-all-devices', 'AuthApiController@logoutAllDevices');
        Route::post('logout-current-device', 'AuthApiController@logoutCurrentDevice');
        Route::post('logout-device', 'AuthApiController@logoutDevice');
    });

    Route::post('request-auth-phone-code', 'AuthApiController@requestAuthPhoneCode');
    Route::post('get-access-token-by-auth-phone-code', 'AuthApiController@getAccessTokenByAuthPhoneCode');
    Route::post('get-access-token-by-auth-email-password', 'AuthApiController@getAccessTokenByEmailPassword');
});
