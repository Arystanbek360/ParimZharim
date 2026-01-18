<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObjectsCollection;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServicesCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;

class EloquentOrderableServiceObjectRepository extends BaseRepository implements OrderableServiceObjectRepository {

    public function getOrderableServiceObjectCollectionByCategory(?int $categoryID): OrderableServiceObjectsCollection
    {
        if (!$categoryID) {
            $objects = OrderableServiceObject::all();
            return new OrderableServiceObjectsCollection($objects);
        }

        $objects = OrderableServiceObject::where('category_id', $categoryID)
            ->where('is_active', true)
            ->orderBy('name', 'asc')->get()->all();
        return new OrderableServiceObjectsCollection($objects);
    }

    public function getOrderedServiceObjectCollectionByCategoryIds(array $categoryIDs): OrderableServiceObjectsCollection
    {
        $objects = OrderableServiceObject::whereIn('category_id', $categoryIDs)->get()->all();
        return new OrderableServiceObjectsCollection($objects);
    }

    public function getAllOrderableServiceObjectCollection(): OrderableServiceObjectsCollection
    {
        $objects = OrderableServiceObject::all();
        return new OrderableServiceObjectsCollection($objects);
    }

    public function getOrderableServiceObjectById(int $id): ?OrderableServiceObject
    {
        return OrderableServiceObject::find($id);
    }

    public function getOrderableServicesByOrderableServiceObjectID(int $serviceObjectID): OrderableServicesCollection
    {
        $serviceObject = $this->getOrderableServiceObjectById($serviceObjectID);
        if (!$serviceObject) {
            return new OrderableServicesCollection();
        }

        //where service category is not 1 and service category is visible to customers and service is active
        $servicesByObject = $serviceObject->services()
            //is not fines
            ->where('service_category_id', '!=', 1)
            ->whereHas('serviceCategory', function ($query) {
                $query->where('is_visible_to_customers', true);
            })
            ->where('is_active', true)
            ->get();
        return new OrderableServicesCollection($servicesByObject);
    }

    /**
     * Загрузить все расписания для объекта
     * @param OrderableServiceObject $serviceObject
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function loadSchedulesForDate(OrderableServiceObject $serviceObject, Carbon $startDate, Carbon $endDate): Collection
    {
        // Выбираем все расписания, которые в диапазоне дат от начала периода до конца периода
        $endDateSchedules = $serviceObject->schedules()
            ->wherePivot('date_from', '<=', $endDate)
            ->wherePivot('date_from', '>=', $startDate)
            ->orderBy('date_from', 'desc')
            ->get();

        // Выбираем крайнее расписание, которое начинается раньше начала периода
        $actualScheduleForStartDate = $serviceObject->schedules()
            ->wherePivot('date_from', '<=', $startDate)
            ->orderBy('date_from', 'desc')
            ->limit(1)
            ->get();

        // Объединяем коллекции
        $mergedSchedules = $endDateSchedules->merge($actualScheduleForStartDate);

        // Удаляем дубликаты
        $uniqueSchedules = $mergedSchedules->unique(function ($item) {
            return $item->pivot->date_from;
        });

        // Сортируем коллекцию
        return $uniqueSchedules->sortByDesc(function ($item) {
            return $item->pivot->date_from;
        })->values(); // values() сбрасывает ключи коллекции
    }

    /**
     * Загрузить все заказы для объекта, которые заканчиваются не позже начала заданного периода -2 дня и начинаются не раньше конца заданного периода +2 дня
     * Бронирования не бывают дольше 1 дня, поэтому для учета бронирований на краях периода добавляем по 2 дня
     * @param OrderableServiceObject $serviceObject
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function loadActualOrdersForDateInterval(OrderableServiceObject $serviceObject, Carbon $startDate, Carbon $endDate): Collection
    {
        $query = $serviceObject->orders()
            ->with(['orderableServiceObject'])
            ->where('end_time', '>', $startDate->copy()->startOfDay()->subDays(2)->toDateTimeString())
            ->where('start_time', '<', $endDate->copy()->endOfDay()->addDays(2)->toDateTimeString())
            ->whereNot('status', OrderStatus::CANCELLED);

        return $query->get();
    }

    /**
     * Сохранить объект
     * @param OrderableServiceObject $orderableServiceObject
     */
    public function save(OrderableServiceObject $orderableServiceObject): void
    {
        $orderableServiceObject->save();
    }
}
