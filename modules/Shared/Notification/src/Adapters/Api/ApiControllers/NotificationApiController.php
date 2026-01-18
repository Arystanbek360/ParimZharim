<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Adapters\Api\ApiControllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Adapters\Api\Transformers\NotificationTransformer;
use Modules\Shared\Notification\Application\GetAllUserNotificationsCount;
use Modules\Shared\Notification\Application\GetNotificationListForUser;
use Modules\Shared\Notification\Application\GetUnreadNotificationCountForUser;
use Modules\Shared\Notification\Application\MarkNotificationsAsRead;
use Throwable;

class NotificationApiController extends BaseApiController
{
    /**
     * Получить список уведомлений для текущего пользователя.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidDataTransformer
     */
    public function getNotifications(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user) {
            return $this->respondError('User not found', 404);
        }

        // Валидация входных параметров
        $validated = $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);

        // Значения по умолчанию
        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 10);

        try {
            $notifications = GetNotificationListForUser::make()->handle($user, $page, $perPage);
            $total = GetAllUserNotificationsCount::make()->handle($user);
            $lastPage = ceil($total / $perPage);

            $transformer = new NotificationTransformer();
            $transformedNotifications = $transformer->collection($notifications);

            return response()->json([
                'notifications' => $transformedNotifications,
                'per_page' => $perPage,
                'current_page' => $page,
                'total' => $total,
                'last_page' => $lastPage,
            ]);

        } catch (Throwable $e) {
            throw new InvalidDataTransformer('Не удалось получить уведомления: ' . $e->getMessage());
        }
    }

    /**
     * Получить количество непрочитанных уведомлений для текущего пользователя.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidDataTransformer
     */
    public function getUnreadNotificationCount(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user) {
            return $this->respondError('User not found', 404);
        }

        try {
            $unreadNotificationCount = GetUnreadNotificationCountForUser::make()->handle($user);

            return response()->json([
                'count' => $unreadNotificationCount,
            ]);

        } catch (Throwable $e) {
            throw new InvalidDataTransformer('Failed to fetch unread notification count: ' . $e->getMessage());
        }
    }

    /**
     * Handle the API request to mark notifications as read.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidDataTransformer
     */
    public function markNotificationsAsRead(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user) {
            return $this->respondError('User not found', 404);
        }

        // Validate input parameters
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $notificationIds = $validated['ids'];

        try {
            MarkNotificationsAsRead::make()->handle($notificationIds, $user);

            return $this->respondSuccess();

        } catch (Throwable $e) {
            throw new InvalidDataTransformer('Failed to mark notifications as read: ' . $e->getMessage());
        }
    }
}
