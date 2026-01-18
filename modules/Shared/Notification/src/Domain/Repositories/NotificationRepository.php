<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserCollection;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\Models\NotificationCollection;
use Modules\Shared\Notification\Domain\Models\NotificationStatus;
use Modules\Shared\Notification\Domain\Models\UserNotificationCollection;

interface NotificationRepository extends BaseRepositoryInterface
{
    /**
     * Получить количество непрочитанных уведомлений пользователя.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadNotificationsCount(int $userId): int;

    /**
     * Получить все уведомления пользователя.
     *
     * @param int $userId
     * @param int $page
     * @param int $perPage
     * @return UserNotificationCollection
     */
    public function getAllUsersNotifications(int $userId, int $page = 1, int $perPage = 10): UserNotificationCollection;

    public function getAllUsersNotificationsCount(int $userId): int;

    /**
     * Получить все уведомления, которые не были отправлены.
     *
     * @return NotificationCollection
     */
    public function getNotSentNotifications(): NotificationCollection;

    public function markNotificationsAsRead(array $notificationIds, int $userId): void;

    public function getUsersWithDeviceToken(): UserCollection;

    public function saveNotification(Notification $notification): void;

    public function createNotificationForUser(User $user, string $title, string $message, string $type, array $metadata = []): void;

    public function getNotNotifiedUsers(Notification $notification): UserCollection;

    public function updateNotificationStatus(Notification $notification, array $userIds, NotificationStatus $status): void;

    public function getAdminsToNotify(): UserCollection;
}
