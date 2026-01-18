<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserCollection;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Modules\Shared\Notification\Domain\Models\Channel;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\Models\NotificationCollection;
use Modules\Shared\Notification\Domain\Models\NotificationStatus;
use Modules\Shared\Notification\Domain\Models\UserNotificationCollection;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;
use Throwable;

class EloquentNotificationRepository extends BaseRepository implements NotificationRepository
{
    /**
     * Получить количество непрочитанных уведомлений пользователя.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadNotificationsCount(int $userId): int
    {
        $count = Notification::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('status', NotificationStatus::SENT)
                ->whereNotNull('sent_at')
                ->whereNull('read_at');
        })->count();

        return $count ?? 0;
    }


    /**
     * Получить все уведомления пользователя, включая данные из связующей таблицы.
     *
     * @param int $userId
     * @param int $page
     * @param int $perPage
     * @return UserNotificationCollection
     */
    public function getAllUsersNotifications(int $userId, int $page = 1, int $perPage = 10): UserNotificationCollection
    {
        $offset = ($page - 1) * $perPage;

        $notifications = Notification::select('notifications_notifications.*', 'notifications_user_to_notification.read_at', 'notifications_user_to_notification.sent_at')
            ->join('notifications_user_to_notification', 'notifications_notifications.id', '=', 'notifications_user_to_notification.notification_id')
            ->where('notifications_user_to_notification.user_id', $userId)
            ->whereNotNull('notifications_user_to_notification.sent_at')
            ->where('notifications_user_to_notification.status', NotificationStatus::SENT)
            ->orderBy('notifications_notifications.planed_send_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return new UserNotificationCollection($notifications);
    }

    public function getAllUsersNotificationsCount(int $userId): int
    {
        $count = Notification::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('status', NotificationStatus::SENT)
                ->whereNotNull('sent_at');
        })->count();

        return $count ?? 0;
    }

    /**
     * Получить все уведомления, которые не были отправлены.
     *
     * @return NotificationCollection
     */
    public function getNotSentNotifications(): NotificationCollection
    {
        $notifications = Notification::whereHas('users', function ($query) {
            $query->where('status', NotificationStatus::CREATED)
                ->whereNull('sent_at');
        })
            ->where('planed_send_at', '<=', now())
            ->with(['users' => function ($query) {
                $query->where('status', NotificationStatus::CREATED)
                    ->whereNull('sent_at');
            }])
            ->orderBy('planed_send_at', 'asc')
            ->get();

        return new NotificationCollection($notifications);
    }

    public function markNotificationsAsRead(array $notificationIds, int $userId): void
    {
        DB::table('notifications_user_to_notification')
            ->where('user_id', $userId)
            ->whereIn('notification_id', $notificationIds)
            ->update(['read_at' => now()]);
    }

    public function getUsersWithDeviceToken(): UserCollection
    {
        $users = User::select('idm_users.*')
            ->join('idm_user_devices', 'idm_users.id', '=', 'idm_user_devices.user_id')
            ->whereNotNull('idm_user_devices.device_token')
            ->distinct()
            ->get();

        return new UserCollection($users);
    }

    public function saveNotification(Notification $notification): void
    {
        $notification->save();
    }

    public function createNotificationForUser(User $user, string $title, string $message, string $type, array $metadata = []): void
    {
        $metadata['for_all_users'] = false;
        $metadata = array_merge($metadata, ['for_all_users' => false]);
        $channel = Channel::PUSH->value;

        DB::beginTransaction();
        try {
            $this->createAndAttachNotification($user, $title, $message, $type, $metadata, $channel);
            DB::commit();
            Log::info("Уведомление успешно создано для пользователя с ID: {$user->id}");
        } catch (Throwable $e) {
            Log::error("Ошибка при создании уведомления: " . $e->getMessage());
            DB::rollBack();
        }
    }

    private function createAndAttachNotification(User $user, string $title, string $message, string $type, array $metadata, string $channel): void
    {
        if ($user->id) {
            $notification = new Notification([
                'title' => $title,
                'body' => $message,
                'metadata' => $metadata,
                'channels' => [$channel],
                'type' => $type,
                'planed_send_at' => now(),
            ]);
            $this->saveNotification($notification);
            $notification->users()->attach($user->id, ['status' => NotificationStatus::CREATED, 'created_at' => now()]);
        }
    }

    public function getNotNotifiedUsers(Notification $notification): UserCollection
    {
        $users = $notification->users()
            ->wherePivot('status', NotificationStatus::CREATED)
            ->wherePivot('sent_at', null)
            ->with('devices')
            ->get();

        return new UserCollection($users);
    }

    public function updateNotificationStatus(Notification $notification, array $userIds, NotificationStatus $status): void
    {
        if (!empty($userIds)) {
            $notification->users()
                ->newPivotStatement()
                ->where('notification_id', $notification->id)
                ->whereIn('user_id', $userIds)
                ->update([
                    'status' => $status->value,
                    'sent_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    public function getAdminsToNotify(): UserCollection
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [Roles::ADMIN, Roles::SUPER_ADMIN]);
        })->get();

        return new UserCollection($admins);
    }

}
