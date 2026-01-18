<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Web\Controllers\LoginController;

Route::get('/login', [LoginController::class, 'login'])->name('login')->middleware('web');
