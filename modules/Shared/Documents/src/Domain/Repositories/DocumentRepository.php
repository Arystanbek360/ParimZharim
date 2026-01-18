<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\DocumentCollection;
use Modules\Shared\Documents\Domain\Models\DocumentQueryParams;

/**
 * Интерфейс `DocumentRepository`
 * Предоставляет методы для работы с документами через запросы к базе данных.
 * Обеспечивают таким образом поддержку операций, определенных на уровне сервисов.
 * Также репозитории отвечают за обработку исключений, возникающих в результате запросов к базе данных.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
interface DocumentRepository extends BaseRepositoryInterface
{
    /**
     * Метод позволяет выполнить поиск по различным атрибутам документа и их комбинациям.
     * @param DocumentQueryParams $queryParams Объект параметров поиска.
     * @return DocumentCollection
     * Возвращает список документов на основе заданных параметров поиска (запроса).
     * Если не было найдено соответствующих результатов, то вернет пустую коллекцию.
     */
    public function getDocumentsByQuery(DocumentQueryParams $queryParams): DocumentCollection;

    /**
     * Возвращает историю версий документа по его идентифицирующему номеру.
     * @param string $number Номер документа.
     * @param int $limit Максмимальное количество возвращаемых версий.
     * @param int $offset Количество пропущенных значений.
     * @return DocumentCollection
     * Возвращает список версий документов по его номеру `number`.
     * Если не было найдено соответствующих результатов, то вернет пустую коллекцию.
     */
    public function getDocumentHistory(string $number, int $limit = 100, int $offset = 0): DocumentCollection;

    /**
     * Сохраняет документ в базе данных.
     * @param Document $document Документ для сохранения.
     * @return void
     */
    public function saveDocument(Document $document): void;

    /**
     * Сохраняет документ в базе данных.
     * @param Document $document Документ для сохранения.
     * @return void
     */
    public function saveDocumentQuietly(Document $document): void;

    public function getMaxVersionNumber(Document $document): ?int;
}
