<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\WebApp\Adapters\Web\Components\Counter;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/counter', Counter::class);
});

Route::get('/', function () {
    return redirect('/nova/login');
})->middleware('web');
