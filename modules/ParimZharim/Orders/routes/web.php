<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('orders')->get('/web-test', function () {
    return "hello web!";
});
