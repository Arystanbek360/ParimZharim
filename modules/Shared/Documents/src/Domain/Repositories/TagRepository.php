<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Domain\Models\TagCollection;

/**
 * Интерфейс `TagRepository`
 * Предоставляет методы для работы с тегами через запросы к базе данных.
 * Обеспечивают таким образом поддержку операций, определенных на уровне сервисов.
 * Также репозитории отвечают за обработку исключений, возникающих в результате запросов к базе данных.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
interface TagRepository extends BaseRepositoryInterface
{
    /**
     * Возвращает теги по идентификаторам.
     * @param array<int> $ids Массив идентификаторов тегов.
     * @return TagCollection
     * Возвращает список тегов. Если не было получено результатов, то вернет пустую коллекцию.
     */
    public function getTagByIds(array $ids): TagCollection;

    /**
     * Возвращает список всех доступных тегов.
     * @param int $limit Максимальное количество записей, которые будут возвращены запросом.
     * @param int $offset Количество пропущенных записей перед тем, как начать возвращать результаты.
     * @return TagCollection
     * Возвращает список тегов. Если не было получено результатов, то вернет пустую коллекцию..
     */
    public function getTags(int $limit = 100, int $offset = 0): TagCollection;

    /**
     * Сохранение тега в базе данных.
     * @param Tag $tag Модель для сохранения.
     * @return void
     */
    public function saveTag(Tag $tag): void;
}
