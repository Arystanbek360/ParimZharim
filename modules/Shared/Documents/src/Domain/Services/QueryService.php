<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Services;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\DocumentCollection;
use Modules\Shared\Documents\Domain\Models\DocumentQueryParams;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Models\PackageCollection;
use Modules\Shared\Documents\Domain\Models\PackageQueryParams;
use Modules\Shared\Documents\Domain\Repositories\DocumentRepository;
use Modules\Shared\Documents\Domain\Repositories\PackageRepository;

/**
 * Сервис `QueryService`
 * Поддерживают различные операции запросов и фильтрации для удобства работы пользователей и администраторов системы.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class QueryService extends BaseService
{
    /**
     * Получить связанный репозиторий документов.
     * @return DocumentRepository
     */
    protected static function getDocumentRepository(): DocumentRepository
    {
        return app(DocumentRepository::class);
    }

    /**
     * Получить связанный репозиторий пакетов.
     * @return PackageRepository
     */
    protected static function getPackageRepository(): PackageRepository
    {
        return app(PackageRepository::class);
    }

    /**
     * Метод позволяет выполнить поиск по различным атрибутам документа и их комбинациям.
     * @param DocumentQueryParams $queryParams Объект параметров поиска (запроса).
     * @return DocumentCollection Возвращает список документов на основе заданных параметров поиска (запроса).
     */
    public static function getDocumentsByQuery(DocumentQueryParams $queryParams): DocumentCollection
    {
        return self::getDocumentRepository()->getDocumentsByQuery($queryParams);
    }

    /**
     * Получение истории версий документа.
     * @param string $number Номер, идентифицирующий документ.
     * @param int $limit Максимальное количество получаемых версий.
     * @param int $offset Количество пропущенных версий.
     * @return DocumentCollection
     */
    public static function getDocumentHistory(string $number, int $limit = 100, int $offset = 0): DocumentCollection
    {
        return self::getDocumentRepository()->getDocumentHistory($number, $limit, $offset);
    }

    /**
     * Получение документа по его номеру и типу.
     * @param string $number Номер, идентифицирующий документ.
     * @param ?string $type Тип документа.
     * @return Document Найденный документ из БД.
     * @throws ValidationError
     */
    public static function getDocument(string $number, ?string $type): Document
    {
        $params = new DocumentQueryParams(types: [$type], numbers: [$number]);
        $documents = self::getDocumentRepository()->getDocumentsByQuery($params);
        return $documents->sortByDesc('version_number')->first();
    }

    /**
     * Получение документов по спискам идентификационных номеров и типов.
     * @param array $numbers Массив номеров, идентифицирующих искомые документы.
     * @param array|null $types Массив типов искомых документов.
     * @return DocumentCollection Массив, найденных документов.
     * @throws ValidationError
     */
    public static function getDocumentsByNumbers(array $numbers, ?array $types): DocumentCollection
    {
        $params = new DocumentQueryParams(types: $types, numbers: $numbers, only_last_version: true);
        return self::getDocumentRepository()->getDocumentsByQuery($params);
    }

    /**
     * @throws ValidationError
     */
    public static function getDocumentById(int $id): ?Document
    {
        $params = new DocumentQueryParams(ids: [$id]);
        $documents = self::getDocumentRepository()->getDocumentsByQuery($params);
        return $documents->first();
    }

    /**
     * Получение документов по типу и датам.
     * @param string $type Тип искомых документов.
     * @param Carbon $date_from_from Дата начала действия документов (нижняя граница).
     * @param Carbon $date_from_to Дата начала действия документов (верхняя граница).
     * @return DocumentCollection
     * @throws ValidationError
     */
    public static function getDocumentsByTypeAndDate(string $type, Carbon $date_from_from, Carbon $date_from_to): DocumentCollection
    {
        $params = new DocumentQueryParams(types: [$type], date_from_from: $date_from_from, date_from_to: $date_from_to, only_last_version: true);
        return self::getDocumentRepository()->getDocumentsByQuery($params);
    }

    /**
     * Получение пакета документов по его идентификатору.
     * @param int $id Идентификатор пакета.
     * @return Package Найденный пакет.
     */
    public static function getPackageById(int $id): Package
    {
        return self::getPackageRepository()->getPackageById($id);
    }

    /**
     * Метод позволяет выполнить поиск по различным атрибутам пакетов документов и их комбинациям.
     * @param PackageQueryParams $queryParams Объект параметров поиска (запроса).
     * @return PackageCollection Возвращает список пакетов документов на основе заданных параметров поиска (запроса).
     */
    public static function getPackagesByQuery(PackageQueryParams $queryParams): PackageCollection
    {
        return self::getPackageRepository()->getPackageByQuery($queryParams);
    }
}
