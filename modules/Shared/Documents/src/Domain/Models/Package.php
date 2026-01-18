<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Documents\Database\Factories\PackageFactory;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Класс `Package` *(Пакет)*
 * Группа документов, объединенная по определенным признакам.
 *
 * @property int $id Уникальный идентификатор пакета.
 * @property string $name Название пакета.
 * @property string $type Тип пакета документов.
 * @property string $status Статус пакета.
 * @property int $creator_id Идентификатор создателя пакета.
 * @property ?int $parent_package_id Идентификатор родительского пакета (необязательное поле).
 * @property array $metadata Метаданные пакета для хранения в формате JSONB.
 * @property BelongsToMany $users Связь с пользователями (многие ко многим).
 * @property AccessMode $access_mode Режим доступа к пакету документов.
 * @property AccessType default_access_type Тип доступа по-умолчанию.
 * @property Carbon $created_at Время создания модели.
 * @property Carbon|null $updated_at Время последнего обновления модели.
 * @property Carbon|null $deleted_at Время удаления модели.
 *
 * @example
 * $package = new Package();
 * $package->name('pack_name');
 * $package->string('PCK-123');
 * <...остальные атрибуты...>
 * $package->save();
 * <или>
 * $package->setRawAttributes($attributes); //array $attributes - атрибуты для модели.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class Package extends BaseModel
{
    use HasFactory, AccessTrait, SoftDeletes;

    /**
     * Связанная с моделью таблица.
     */
    protected $table = 'documents_packages';

    /**
     * Атрибуты, которые разрешено массово назначать (Mass Assignment).
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'status',
        'creator_id',
        'parent_package_id',
        'metadata',
        'access_mode',
        'default_access_type',
    ];

    /**
     * Атрибуты, которые должны быть приведены к соответствующим типам.
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'access_mode' => AccessMode::class,
        'default_access_type' => AccessType::class,
    ];

    /**
     * @var array Атрибуты по умолчанию.
     */
    protected $attributes = [
        'default_access_type' => AccessType::READ,
        'access_mode' => AccessMode::SPECIFIC_USERS,
    ];

    /**
     * Получить пользователей, связанных с пакетом документов.
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'documents_package_to_user', 'package_id', 'user_id')
            ->using(PackageToUserPivot::class)
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Создает новый экземпляр фабрики для модели.
     * @return PackageFactory
     */
    protected static function newFactory(): PackageFactory
    {
        return PackageFactory::new();
    }
}
