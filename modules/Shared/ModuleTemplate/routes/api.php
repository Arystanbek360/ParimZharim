<?php declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api/module-template')->get('/api-test', function (Request $request) {
    return "hello api from module template!";
})->middleware('auth:sanctum');
