<?php declare(strict_types=1);

use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;
use Modules\Shared\CMS\Adapters\Api\ApiControllers\ContentApiController;

Route::prefix('api/cms')
    ->middleware(HandleCors::class)
    ->group(function () {
        Route::get('/get-content-by-slug', [ContentApiController::class, 'getContentBySlug']);
        Route::get('/get-content-by-category', [ContentApiController::class, 'getContentByCategory']);
    });
