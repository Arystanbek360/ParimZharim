<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObjectsCollection;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServicesCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface OrderableServiceObjectRepository extends BaseRepositoryInterface {
    public function getOrderableServiceObjectById(int $id): ?OrderableServiceObject;

    public function getOrderableServiceObjectCollectionByCategory(int $categoryID): OrderableServiceObjectsCollection;

    public function getOrderedServiceObjectCollectionByCategoryIds(array $categoryIDs): OrderableServiceObjectsCollection;

    public function getAllOrderableServiceObjectCollection(): OrderableServiceObjectsCollection;

    public function getOrderableServicesByOrderableServiceObjectID(int $serviceObjectID): OrderableServicesCollection;

    public function loadSchedulesForDate(OrderableServiceObject $serviceObject, Carbon $startDate, Carbon $endDate): Collection;

    public function loadActualOrdersForDateInterval(OrderableServiceObject $serviceObject, Carbon $startDate, Carbon $endDate): Collection;

    public function save(OrderableServiceObject $orderableServiceObject): void;

}
