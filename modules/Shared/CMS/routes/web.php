<?php declare(strict_types=1);


use Illuminate\Support\Facades\Route;
use Modules\Shared\CMS\Adapters\Web\Components\DeleteAccountFormComponent;
use Modules\Shared\CMS\Adapters\Web\Components\PrivacyPolicyComponent;


Route::middleware('web')->group(function () {
    Route::get('/privacy-policy', PrivacyPolicyComponent::class)->name('privacy-policy');
    Route::get('/delete-account', DeleteAccountFormComponent::class)->name('delete-account');
});
