<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('module-template')->group(function() {
    Route::get('api-test', function () {
        return "hello api from module template!";
    })->middleware('auth:sanctum');
});
