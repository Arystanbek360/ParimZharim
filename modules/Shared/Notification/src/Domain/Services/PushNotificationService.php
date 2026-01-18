<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Services;

use Modules\Shared\Core\Domain\BaseServiceInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserCollection;
use Modules\Shared\Notification\Domain\Models\Notification;

interface PushNotificationService extends BaseServiceInterface
{

    public static function send(Notification $notification, UserCollection $users): void;
}
