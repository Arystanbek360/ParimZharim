<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BasePivot;
use Modules\Shared\Documents\Domain\Services\QueryService;

/**
 * Класс `PackageToUserPivot`
 * Модель, которая связывает пакет документов с конкретным пользователем.
 *
 * @property int $package_id Идентификатор документа.
 * @property int $user_id Идентификатор пользователя.
 * @property AccessType $access_type Тип доступа пользователя к пакету документов.
 * @property Carbon $created_at Дата создания записи.
 * @property Carbon|null $updated_at Дата последнего обновления записи.
 *
 * @example Создание новой связи между документом и пользователем
 * $pivot = new PackageToUserPivot();
 * $pivot->package_id = 1;
 * $pivot->user_id = 2;
 * $pivot->access_type = AccessType::READ;
 * $pivot->save();
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class PackageToUserPivot extends BasePivot
{
    /**
     * @var string Таблица, связанная с текущей моделью.
     */
    protected $table = 'documents_package_to_user';

    /**
     * @var array Атрибуты, которые можно массово назначать.
     */
    protected $fillable = [
        'package_id',
        'user_id',
        'access_type',
    ];

    /**
     * @var array Атрибуты, которые должны быть приведены к нативным типам (Касты атрибутов модели).
     */
    protected $casts = [
        'access_type' => AccessType::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Если при создании связи пользователя и пакета не был задан `access_type`, то назначается доступ по-умолчанию.
        static::creating(function ($pivot) {
            if ($pivot->access_type === null) {
                $package = QueryService::getPackageById($pivot->package_id);
                $pivot->access_type = $package->default_access_type;
            }
        });
    }
}
