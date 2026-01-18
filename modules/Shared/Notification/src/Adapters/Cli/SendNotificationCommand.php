<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Adapters\Cli;

use Modules\Shared\Core\Adapters\Cli\BaseCommand;
use Modules\Shared\Notification\Adapters\Job\SendNotification;

class SendNotificationCommand extends BaseCommand
{
    protected $signature = 'notification:send';
    protected $description = 'Отправляет push уведомления каждую минуту';

    public function handle(): void
    {
        SendNotification::dispatch()->onQueue('notifications');
    }
}
