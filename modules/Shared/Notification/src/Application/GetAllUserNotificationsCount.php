<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;

class GetAllUserNotificationsCount extends BaseAction
{

    public function __construct(
        private readonly NotificationRepository $notificationRepository
    ) {}

    public function handle(User $user): int
    {
        return $this->notificationRepository->getAllUsersNotificationsCount($user->id);
    }

}
