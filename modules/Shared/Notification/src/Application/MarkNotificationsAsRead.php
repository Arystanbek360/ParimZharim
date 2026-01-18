<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Models\Channel;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;

class MarkNotificationsAsRead extends BaseAction
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
    )
    {}

    public function handle(array $notificationIds, User $user): void
    {
        $this->notificationRepository->markNotificationsAsRead($notificationIds, $user->id);
    }

}
