<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Services;

use Modules\Shared\Core\Domain\BaseDomainService;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserCollection;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\Models\NotificationStatus;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;

class NotificationService extends BaseDomainService
{

    public static function getNotificationRepository(): NotificationRepository
    {
        return app(NotificationRepository::class);
    }

    public static function assignNotificationToEveryone(Notification $notification): void
    {
        $usersWithDeviceToken = self::getNotificationRepository()->getUsersWithDeviceToken();
        $chunks = array_chunk($usersWithDeviceToken->all(), 1000);

        foreach ($chunks as $chunk) {
            $attachments = [];

            foreach ($chunk as $user) {
                $attachments[$user->id] = [
                    'status' => NotificationStatus::CREATED,
                    'created_at' => now(),
                ];
            }

            $notification->users()->attach($attachments);
        }
    }

    public static function detachNotificationByDeleting(Notification $notification): void
    {
        $notification->users()->wherePivot('status', NotificationStatus::CREATED)->detach();
    }

    public static function updateNotificationStatus(Notification $notification, array $failedUserIds, array $successfulUserIds): void
    {
        $failedUserIds = array_diff($failedUserIds, $successfulUserIds);

        self::getNotificationRepository()->updateNotificationStatus($notification, $successfulUserIds, NotificationStatus::SENT);
        self::getNotificationRepository()->updateNotificationStatus($notification, $failedUserIds, NotificationStatus::FAILED);
    }

    public static function collectDeviceTokens(UserCollection $users): array
    {
        $deviceTokens = [];
        $tokenToUserIdMap = [];

        foreach ($users as $user) {
            $tokens = $user->devices->pluck('device_token')->filter();

            foreach ($tokens as $token) {
                $deviceTokens[] = $token;
                $tokenToUserIdMap[$token] = $user->id;
            }
        }

        return [$deviceTokens, $tokenToUserIdMap];
    }

}
