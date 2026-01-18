<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Adapters\Job;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Modules\Shared\Core\Adapters\Job\BaseJob;
use Modules\Shared\Notification\Application\SendAllNotSentNotifications;

class SendNotification extends BaseJob implements ShouldBeUnique
{
    /**
     * Время блокировки уникальной задачи (в секундах).
     * Например, 60 секунд.
     */
    public int $uniqueFor = 60;

    /**
     * Основной метод обработки задачи.
     */
    public function handle(): void
    {
        SendAllNotSentNotifications::make()->handle();
    }

    /**
     * Определение уникального ключа для задачи.
     * Это предотвращает дублирование задач.
     */
    public function uniqueId(): string
    {
        return 'send_notification_job';
    }
}
