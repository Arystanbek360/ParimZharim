<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObjectsCollection;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetOrderableServiceObjectsForReservationTable extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(array $categoryIds, Carbon|string|null $date): OrderableServiceObjectsCollection
    {
        $date = match (true) {
            is_string($date) => Carbon::parse($date),
            is_null($date) => Carbon::now(),
            default => $date,
        };

        $timezone = 'Asia/Almaty';

        $date->shiftTimezone($timezone);

        if ($date < Carbon::now()->setTimezone($timezone)) {
            $date = Carbon::now()->setTimezone($timezone);
        }

        if (count($categoryIds) === 0) {
            $objects = $this->orderableServiceObjectRepository->getAllOrderableServiceObjectCollection();
        } else {
            $objects = $this->orderableServiceObjectRepository->getOrderedServiceObjectCollectionByCategoryIds($categoryIds);
        }

        return $objects->map(function ($object) use ($date) {
            $object->merged_free_time_slots = GetMergedFreeSlotsForServiceObjectOnDate::make()->handle($object->id, $date, $date->copy()->addMinutes(47.5 * 60));
            return $object;
        });
    }
}
