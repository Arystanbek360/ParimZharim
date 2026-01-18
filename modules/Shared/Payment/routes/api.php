<?php declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Shared\Payment\Adapters\Api\ApiControllers\PaymentApiController;
use Modules\Shared\Payment\Adapters\Api\ApiControllers\Webhooks\CloudPaymentWebhookController;
use Modules\Shared\Payment\Adapters\Api\ApiControllers\Webhooks\TipTopPayWebhookController;

Route::prefix('api/payments')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/create-payment', [PaymentApiController::class, 'createPayment']);
        Route::get('/payment-methods', [PaymentApiController::class, 'getPaymentsMethodsForMobile']);
        Route::get('/get-payments-for-order', [PaymentApiController::class, 'getPaymentsForOrder']);
    });

    // Cloud Payment Webhooks
    Route::prefix('webhooks/cloudpayments')->group(function () {
        Route::post('/check', function (Request $request) {
            return (new CloudPaymentWebhookController)->handleWebhook($request, 'check');
        });
        Route::post('/confirm', function (Request $request) {
            return (new CloudPaymentWebhookController)->handleWebhook($request, 'confirm');
        });
        Route::post('/pay', function (Request $request) {
            return (new CloudPaymentWebhookController)->handleWebhook($request, 'pay');
        });
        Route::post('/fail', function (Request $request) {
            return (new CloudPaymentWebhookController)->handleWebhook($request, 'fail');
        });
    });

    // TipTopPay Webhooks
    Route::prefix('webhooks/tiptoppay')->group(function () {
        Route::post('/check', function (Request $request) {
            return (new TipTopPayWebhookController)->handleWebhook($request, 'check');
        });
        Route::post('/confirm', function (Request $request) {
            return (new TipTopPayWebhookController)->handleWebhook($request, 'confirm');
        });
        Route::post('/pay', function (Request $request) {
            return (new TipTopPayWebhookController)->handleWebhook($request, 'pay');
        });
        Route::post('/fail', function (Request $request) {
            return (new TipTopPayWebhookController)->handleWebhook($request, 'fail');
        });
    });
});
