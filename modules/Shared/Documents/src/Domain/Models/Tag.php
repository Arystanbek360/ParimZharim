<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Documents\Database\Factories\TagFactory;

/**
 * Класс `Tag` *(Тег)*
 * Метка, которая используется для классификации документов.
 *
 * @property int $id Уникальный идентификатор тега.
 * @property string $name Название тега.
 * @property Carbon $created_at Время создания модели.
 * @property Carbon|null $updated_at Время последнего обновления модели.
 * @property Carbon|null $deleted_at Время удаления модели.
 *
 * @example
 * $tag = new Tag();
 * $tag->name('tag_name');
 * $tag->save();
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class Tag extends BaseModel
{
    use HasFactory;

    /**
     * Связанная с моделью таблица.
     */
    protected $table = 'documents_tags';

    /**
     * Атрибуты, которые разрешено массово назначать (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Создает новый экземпляр фабрики для модели.
     *
     * @return TagFactory
     */
    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
