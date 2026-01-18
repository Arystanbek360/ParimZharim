<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObjectsCollection;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetOrderableServiceObjectsByCategoryOnDate extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(?int $categoryID, Carbon|string|null $date): OrderableServiceObjectsCollection
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

       $objects = $this->orderableServiceObjectRepository->getOrderableServiceObjectCollectionByCategory($categoryID);

        // returns objects with merged free time slots for 24 hours
        return $objects->map(function ($object) use ($date) {
            $object->merged_free_time_slots = GetMergedFreeSlotsForServiceObjectOnDate::make()->handle($object->id, $date, $date->copy()->addMinutes(30 * 60), 60);
            return $object;
        });
    }
}
