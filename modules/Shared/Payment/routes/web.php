<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Shared\Payment\Adapters\Web\CardWidgetComponent;
use Modules\Shared\Payment\Adapters\Web\TipTopPayThreeDsController;

Route::middleware('web')->group(function () {
    // The route now accepts a payment_id as a parameter
    Route::get('/payment-widget/{paymentID}', CardWidgetComponent::class)
        ->name('payment-widget');

    Route::get('/payment-widget/{paymentID}/3ds', [TipTopPayThreeDsController::class, 'showChallenge'])
        ->name('payment.tiptoppay.3ds');
});
