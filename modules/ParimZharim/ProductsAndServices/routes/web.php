<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('products-services')->get('/web-test', function () {
    return "hello web!";
});
