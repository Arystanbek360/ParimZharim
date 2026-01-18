<?php

use Illuminate\Support\Facades\Route;
use Modules\ParimZharim\Orders\NovaComponents\ReservationsTool\Http\Controllers\ReservationsController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/reservations', ReservationsController::class . '@index');
Route::get('/categories', ReservationsController::class . '@objectCategories');
