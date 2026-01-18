<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Illuminate\Support\Facades\App;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserCollection;
use Modules\Shared\Notification\Domain\Models\Channel;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;
use Modules\Shared\Notification\Domain\Services\NotificationService;

class SendAllNotSentNotifications extends BaseAction
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
    ) {}

    public function handle(): void
    {
        $notifications = $this->notificationRepository->getNotSentNotifications();

        foreach ($notifications as $notification) {
            $this->processNotification($notification);
        }
    }

    private function processNotification(Notification $notification): void
    {
        foreach ($notification->channels as $channel) {
            $this->sendNotificationThroughChannel($notification, $channel);
        }
    }

    private function sendNotificationThroughChannel(Notification $notification, string $channel): void
    {
        $channel = Channel::from($channel);
        $channelServiceClassName = GetNotificationServiceByChanel::make()->handle($channel);

        // Получаем экземпляр класса через контейнер
        $channelService = App::make($channelServiceClassName);

        $users = $this->getUsersToNotify($notification);

        if (!$users->isEmpty()) {
            $channelService->send($notification, $users); // Вызываем метод send
        }
    }

    private function getUsersToNotify(Notification $notification): UserCollection
    {
        if (empty($notification->users) && $notification->for_all_users) {
            NotificationService::assignNotificationToEveryone($notification);
        }

        return $this->notificationRepository->getNotNotifiedUsers($notification);
    }
}
