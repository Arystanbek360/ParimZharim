<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Класс `Document` *(Документ)*
 * Основная сущность, представляющая абстрактный документ в системе.
 *
 * @property int $id Уникальный идентификатор документа.
 * @property string $name Название документа.
 * @property string $number Уникальный номер документа.
 * @property string $type Тип документа.
 * @property string $status Статус документа.
 * @property int $version_number Номер версии документа.
 * @property int $creator_id Идентификатор создателя документа.
 * @property int $editor_id Идентификатор редактора документа.
 * @property ?int $package_id Идентификатор пакета, к которому относится документ (необязательное поле).
 * @property Package $package Связь с пакетом документов (необязательное поле).
 * @property ?string $file Путь к файлу (необязательное поле).
 * @property ?array $content Произвольный контент для хранения в формате JSONB.
 * @property ?array $metadata Метаданные документа для хранения в формате JSONB.
 * @property Carbon $date_from Дата начала действия документа.
 * @property ?Carbon $date_to Дата окончания действия документа (необязательное поле).
 * @property BelongsToMany $tags Связь с тегами (многие ко многим).
 * @property BelongsToMany $users Связь с пользователями (многие ко многим).
 * @property AccessMode $access_mode Режим доступа к документов.
 * @property AccessType $default_access_type Тип доступа по-умолчанию.
 * @property Carbon $created_at Время создания модели.
 * @property Carbon|null $updated_at Время последнего обновления модели.
 * @property Carbon|null $deleted_at Время удаления модели.
 *
 * @example
 * $document = Document::makeDocumentInstance($attributes);
 * $document->save();
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
abstract class Document extends BaseModel
{
    use AccessTrait;

    /**
     * Связанная с моделью таблица.
     */
    protected $table = 'documents_documents';

    /**
     * Атрибуты, которые разрешено массово назначать (Mass Assignment).
     * @var array
     */
    protected $fillable = [
        'name',
        'number',
        'type',
        'status',
        'creator_id',
        'editor_id',
        'package_id',
        'file',
        'content',
        'metadata',
        'date_from',
        'date_to',
        'access_mode',
        'default_access_type',
    ];

    /**
     * Атрибуты, которые должны быть приведены к соответствующим типам.
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'content' => 'array',
        'date_from' => 'datetime',
        'date_to' => 'datetime',
        'access_mode' => AccessMode::class,
        'default_access_type' => AccessType::class,
    ];

    /**
     * @var array Атрибуты по умолчанию.
     */
    protected $attributes = [
        'default_access_type' => AccessType::READ,
        'access_mode' => AccessMode::SPECIFIC_USERS,
        'version_number' => 1,
    ];

    /**
     * Абстрактный метод определения типа **конкретного** класса-наследника от `ContentStructure`.
     * @return string
     * Возвращает название класса, наследующего `ContentStructure` в каждом конкретном случае реализации.
     * @example return NakladnayaContentStructure::class;
     */
    abstract protected function getContentStructure(): string;

    /**
     * Проверяет свойство `content` на соответсвие классу от `ContentStructure` в каждом конкретном случае наследования.
     * @return bool Вернет `true`, если  соответствует типу, `false` - если не соответсвует.
     */
    public function validateContentStructure(): bool
    {
        return get_class((object)$this->content) === $this->getContentStructure();
    }

    /**
     * Получить пакет, связанный с документом.
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Получить теги, связанные с документом.
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'documents_tag_to_document', 'document_id', 'tag_id');
    }

    /**
     * Получить пользователей, связанных с документом.
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'documents_document_to_user', 'document_id', 'user_id')
            ->using(DocumentToUserPivot::class)
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Создает экземпляр документа через анонимный класс.
     * @param array $attributes Аттрибуты для назначения модели.
     * @return Document Экземпляр документа.
     */
    public static function makeDocumentInstance(array $attributes = []): Document
    {
        $document = new class extends Document {
            protected function getContentStructure(): string
            {
                $object = (object)$this->content;
                return $object::class;
            }
        };
        $document->fill($attributes);
        // назначение атрибутов недоступных для массового назначения
        if (key_exists('id', $attributes)) {
            $document->id = $attributes['id'];
        }
        $document->version_number = $attributes['version_number'] ?? $document->version_number;
        $document->created_at = $attributes['created_at'] ?? $document->created_at;
        $document->updated_at = $attributes['updated_at'] ?? $document->updated_at;
        $document->deleted_at = $attributes['deleted_at'] ?? $document->deleted_at;

        return $document;
    }

//    /**
//     * Создает новый экземпляр фабрики для модели.
//     * @return DocumentFactory
//     */
//    protected static function newFactory(): DocumentFactory
//    {
//        return DocumentFactory::new();
//    }
}
