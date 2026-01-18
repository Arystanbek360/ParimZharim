<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Domain\Models\Channel;
use Modules\Shared\Notification\Domain\Services\DefaultNotificationService;
use Modules\Shared\Notification\Domain\Services\PushNotificationService;

class GetNotificationServiceByChanel extends BaseAction
{

    public function handle(Channel $channel): string
    {
        return match ($channel) {
            Channel::PUSH => PushNotificationService::class,
            default => DefaultNotificationService::class,
        };
    }
}
