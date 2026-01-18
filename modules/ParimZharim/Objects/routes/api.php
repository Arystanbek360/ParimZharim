<?php declare(strict_types=1);

use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\Objects\Adapters\Api\ApiControllers\ObjectsApiController;

Route::prefix('api/objects')->middleware(HandleCors::class)->group(function () {
    Route::get('/get-object-categories', [ObjectsApiController::class, 'getCategories']);
    Route::get('/get-object-tags', [ObjectsApiController::class, 'getTags']);
});
