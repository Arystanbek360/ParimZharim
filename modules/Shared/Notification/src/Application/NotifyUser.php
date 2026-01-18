<?php

namespace Modules\Shared\Notification\Application;

use Laravel\Nova\Notifications\NovaNotification;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;


class NotifyUser extends BaseAction
{
    public function handle(User $user, string $message): void
    {
        $user->notify(NovaNotification::make()
            ->message($message)
        );
    }
}
