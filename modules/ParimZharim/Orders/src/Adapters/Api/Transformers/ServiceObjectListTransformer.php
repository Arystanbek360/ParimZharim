<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Api\Transformers;

use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class ServiceObjectListTransformer extends BaseTransformer
{

    public function transform(OrderableServiceObject|BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        $serviceObject = $data['serviceObject'];

        $time = $serviceObject->merged_free_time_slots;

        $availableTime = array_map(function ($time) {
            return [
                'time_from' => $time['start'],
                'time_to' => $time['end'],
                'color' => '#36A840',
            ];
        }, $time);

        $photos = array_merge(
            [$serviceObject->getFirstMediaUrl('main')],
            $serviceObject->getMedia('gallery')->map(function ($media) {
                return $media->getUrl();
            })->toArray()
        );

        return [
            'available' => (bool)$time,
            'id' => $serviceObject->id,
            'capacity' => $serviceObject->capacity,
            'name' => $serviceObject->name,
            'type' => $serviceObject->category->id,
            'price' => $data['price'] ?? [],
            'kitchen_deposit' => (int) $data['kitchen_deposit'] ?? null,
            'photos' => $photos,
            'available_time' => $availableTime,
            'min_hours' => (int) $data['min_hours'],
            'confirmation_waiting_duration' => (int) $data['confirmation_waiting_duration'],
        ];
    }
}
