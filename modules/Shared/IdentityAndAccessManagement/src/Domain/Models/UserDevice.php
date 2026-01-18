<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Класс UserDevice *(Устройство пользователя)*
 *
 * Основная сущность, представляющая привязанное к пользователю устройство в системе.
 *
 * @property int $user_id Идентификатор пользователя.
 * @property string $device_id Идентификатор устройства.
 * @property Carbon $created_at Время создания записи.
 * @property Carbon $updated_at Время последнего обновления записи.
 * @property Carbon $deleted_at Время удаления записи.
 *
 * @example
 * $attributes = [
 *        'user_id' => 1,
 *        'device_id' => 'device_456',
 *        ];
 * $userDevice = new UserDevice($attributes);
 * $userDevice->save();
 *
 * @version 1.0.0
 * @since 2024-10-07
 *
 */
class UserDevice extends BaseModel
{
    use SoftDeletes;

    protected $table = 'idm_user_devices';

    protected $fillable = [
        'user_id',
        'device_id',
    ];

    /**
     * Связь с пользователем (User).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
