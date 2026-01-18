<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Shared\Notification\Adapters\Api\ApiControllers\NotifiableUserDeviceController;
use Modules\Shared\Notification\Adapters\Api\ApiControllers\NotificationApiController;

Route::prefix('api/notifications')->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/get-notifications', [NotificationApiController::class, 'getNotifications']);
            Route::get('/get-unread-notification-count', [NotificationApiController::class, 'getUnreadNotificationCount']);
            Route::post('/mark-as-read', [NotificationApiController::class, 'markNotificationsAsRead']);
        });
    });

Route::prefix('api/user-device')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/update-device-token', [NotifiableUserDeviceController::class, 'updateUserDevice']);
    });
});

