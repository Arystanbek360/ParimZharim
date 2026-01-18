<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Models\UserNotificationCollection;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;

class GetNotificationListForUser extends BaseAction
{

    public function __construct(
        private readonly NotificationRepository $notificationRepository
    ) {}

    /**
     * Получить список уведомлений для пользователя.
     *
     * @param User $user
     * @param int $page
     * @param int $perPage
     * @return UserNotificationCollection
     */
    public function handle(User $user, int $page = 1, int $perPage = 10): UserNotificationCollection
    {
        return $this->notificationRepository->getAllUsersNotifications($user->id, $page, $perPage);
    }


}
