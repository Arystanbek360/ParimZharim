<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Domain\Models\Channel;
use Modules\Shared\Notification\Domain\Models\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;
use Modules\Shared\Notification\Domain\Services\NotificationService;
use Throwable;

class CreateNotification extends BaseAction
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository
    ) {}
    public function handle(User $user, string $title, string $message, string $type, array $metadata = []): void
    {
        $this->notificationRepository->createNotificationForUser($user, $title, $message, $type, $metadata);
    }
}
