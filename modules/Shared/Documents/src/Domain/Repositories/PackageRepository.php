<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Models\PackageCollection;
use Modules\Shared\Documents\Domain\Models\PackageQueryParams;

/**
 * Интерфейс `PackageRepository`
 * Предоставляет методы для работы с пакетами документов через запросы к базе данных.
 * Обеспечивают таким образом поддержку операций, определенных на уровне сервисов.
 * Также репозитории отвечают за обработку исключений, возникающих в результате запросов к базе данных.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
interface PackageRepository extends BaseRepositoryInterface
{
    /**
     * Сохранение документа в конкретный пакет.
     * @param Document $document Документ для сохранения.
     * @param Package $package Пакет для сохранения.
     * @return void
     */
    public function saveDocumentInPackage(Document $document, Package $package): void;

    /**
     * Метод позволяет выполнить поиск по различным атрибутам пакета документов и их комбинациям.
     * @param PackageQueryParams $queryParams Объект параметров поиска.
     * @return PackageCollection|null
     * Возвращает список пакетов на основе заданных параметров поиска (запроса).
     * Если не было получено результатов, то вернет `null`.
     */
    public function getPackageByQuery(PackageQueryParams $queryParams): PackageCollection|null;

    /**
     * Получить пакет документов по его идентификатору.
     * @param int $packageId Идентификатор пакета.
     * @return Package|null Возвращает найденную модель пакета документов,`null` - если модель не найдена.
     */
    public function getPackageById(int $packageId): Package|null;

    /**
     * Сохранение пакета в базе данных.
     * @param Package $package Модель для сохранения.
     * @return void
     */
    public function savePackage(Package $package): void;
}

