<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Services\NotificationService;

/**
 * Класс Notification *(Уведомление)*
 *
 * Основная сущность, представляющая уведомление в системе.
 *
 * @property string $id Уникальный идентификатор уведомления.
 * @property string $title Заголовок уведомления.
 * @property string $body Текст уведомления.
 * @property array $metadata Метаданные уведомления.
 * @property array $channels Каналы отправки уведомления.
 * @property string $type Тип уведомления.
 * @property Carbon $planed_send_at Планируемое время отправки уведомления.
 * @property Carbon $created_at Время создания уведомления.
 * @property Carbon $updated_at Время последнего обновления уведомления.
 * @property Carbon $deleted_at Время удаления уведомления.
 * @property bool $for_all_users Признак отправки уведомления всем пользователям.
 *
 * @example
 * $attributes = [
 *        'title' => 'Заголовок уведомления',
 *        'body' => 'Текст уведомления',
 *        'metadata' => ['key' => 'value'],
 *        'channels' => ['email', 'sms'],
 *        'type' => 'info',
 *        'planed_send_at' => Carbon::now(),
 *        ];
 * $notification = new Notification($attributes);
 * $notification->save();
 *
 * @version 1.0.0
 * @since 2024-10-07
 *
 */
class Notification extends BaseModel
{
    use SoftDeletes;

    protected $table = 'notifications_notifications';

    protected $fillable = [
        'title',
        'body',
        'metadata',
        'channels',
        'type',
        'planed_send_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'channels' => 'array',
        'planed_send_at' => 'datetime',
    ];

    protected $attributes = [
        'channels' => '["push"]',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notifications_user_to_notification', 'notification_id', 'user_id')
            ->using(UserToNotificationPivot::class)
            ->withPivot('status', 'read_at', 'sent_at');
    }

    public function getForAllUsersAttribute(): bool
    {
        return $this->metadata['for_all_users'] ?? true;
    }

    public function setForAllUsersAttribute(bool $value): void
    {
        $metadata = $this->metadata;
        $metadata['for_all_users'] = $value;
        $this->metadata = $metadata;
    }

    public static function boot(): void
    {
        parent::boot();

        static::created(function (Notification $notification) {
            if ($notification->for_all_users) {
                NotificationService::assignNotificationToEveryone($notification);
            }
        });

        static::deleting(function (Notification $notification) {
            NotificationService::detachNotificationByDeleting($notification);
        });
    }
}
