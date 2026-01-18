<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Infrastructure\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserCollection;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\Models\NotificationStatus;
use Modules\Shared\Notification\Domain\Repositories\NotifiableUserDeviceRepository;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;
use Modules\Shared\Notification\Domain\Services\NotificationService;
use Modules\Shared\Notification\Domain\Services\PushNotificationService;
use Throwable;

class FirebaseService extends BaseService implements PushNotificationService
{
    private static Messaging $messaging;

    const int MAX_TOKENS_PER_BATCH = 500;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('services.notification_firebase_credentials'));

        self::$messaging = $factory->createMessaging();
    }

    public static function getNotifiableUserDeviceRepository(): NotifiableUserDeviceRepository
    {
        return app(NotifiableUserDeviceRepository::class);
    }

    /**
     * @throws Throwable
     */
    public static function send(Notification $notification, UserCollection $users): void
    {
        if (!self::$messaging) {
            Log::info(config('services.notifications_firebase.credentials'));
            self::initializeMessaging();
        }

        [$deviceTokens, $tokenToUserIdMap] = NotificationService::collectDeviceTokens($users);

        if (empty($deviceTokens)) {
            return;
        }

        [$successfulUserIds, $failedUserIds] = self::sendNotificationToBatches($notification, $deviceTokens, $tokenToUserIdMap);

        NotificationService::updateNotificationStatus($notification, $failedUserIds, $successfulUserIds);
    }

    private static function initializeMessaging(): void
    {
        $serviceAccount = config('services.notification_firebase.credentials');
        $factory = (new Factory)
            ->withServiceAccount($serviceAccount);

        self::$messaging = $factory->createMessaging();
    }

    /**
     * @throws Throwable
     */
    private static function sendNotificationToBatches(Notification $notification, array $deviceTokens, array $tokenToUserIdMap): array
    {
        $successfulUserIds = [];
        $failedUserIds = [];

        $tokenChunks = array_chunk($deviceTokens, self::MAX_TOKENS_PER_BATCH, false);

        foreach ($tokenChunks as $tokensBatch) {
            try {
                $report = self::sendBatch($notification, $tokensBatch);

                $successfulUserIds = array_merge($successfulUserIds, self::processSuccesses($report, $tokenToUserIdMap));
                $failedUserIds = array_merge($failedUserIds, self::processFailures($report, $tokenToUserIdMap));
            } catch (Throwable $e) {
                Log::error('Error sending push notifications: ' . $e->getMessage());
                throw $e;
            }
        }

        // Исключаем успешных пользователей из списка неудач
        $failedUserIds = array_diff($failedUserIds, $successfulUserIds);

        return [array_unique($successfulUserIds), array_unique($failedUserIds)];
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     */
    private static function sendBatch(Notification $notification, array $tokensBatch): MulticastSendReport
    {
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $notification->title,
                'body' => $notification->body,
            ])
            ->withData($notification->metadata ?? []);
        return self::$messaging->sendMulticast($message, $tokensBatch);
    }

    private static function processSuccesses(MulticastSendReport $report, array $tokenToUserIdMap): array
    {
        $successfulUserIds = [];
        $validTokens = $report->validTokens();

        foreach ($validTokens as $token) {
            $userId = $tokenToUserIdMap[$token] ?? null;
            if ($userId) {
                $successfulUserIds[] = $userId;
            }
        }

        return $successfulUserIds;
    }

    private static function processFailures(MulticastSendReport $report, array $tokenToUserIdMap): array
    {
        $failedUserIds = [];
        $invalidTokens = $report->invalidTokens();

        foreach ($invalidTokens as $token) {
            $userId = $tokenToUserIdMap[$token] ?? null;
            if ($userId) {
                $failedUserIds[] = $userId;
                self::getNotifiableUserDeviceRepository()->cleanDeviceToken($userId, $token);
            }
        }

        return $failedUserIds;
    }
}
