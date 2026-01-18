<?php

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;

class NotifyAllAdmins extends BaseAction
{

    public function __construct(
        private readonly NotificationRepository $notificationRepository,
    )
    {}
    public function handle(string $message): void
    {
        $adminsToNotify = $this->notificationRepository->getAdminsToNotify();

        foreach ($adminsToNotify as $admin) {
            NotifyUser::make()->handle($admin, $message);
        }
    }
}
