<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Models;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BasePivot;

/**
 * Класс UserToNotificationPivot *(Связь уведомлений с пользователями)*
 *
 * Сущность, представляющая связь между уведомлениями и пользователями.
 *
 * @property int $notification_id Уникальный идентификатор уведомления.
 * @property int $user_id Уникальный идентификатор пользователя.
 * @property NotificationStatus $status Статус уведомления для пользователя.
 * @property Carbon $sent_at Время отправки уведомления пользователю.
 * @property Carbon $read_at Время прочтения уведомления пользователем.
 * @property Carbon $created_at Время создания связи.
 * @property Carbon $updated_at Время последнего обновления связи.
 */
class UserToNotificationPivot extends BasePivot
{
    protected $table = 'notifications_user_to_notification';

    protected $casts = [
        'status' => NotificationStatus::class,
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

}
