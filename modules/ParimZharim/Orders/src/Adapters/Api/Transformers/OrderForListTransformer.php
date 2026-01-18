<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Api\Transformers;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Application\Actions\GetOrderableServiceObjectByID;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class OrderForListTransformer extends BaseTransformer
{

    public function transform(Order|BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        $status = match ($data->status) {
            OrderStatus::CREATED => 'created',
            OrderStatus::CONFIRMED => 'confirmed',
            OrderStatus::CANCELLED => 'cancelled',
            OrderStatus::COMPLETED => 'completed',
            OrderStatus::CANCELLATION_REQUESTED => 'cancellation_requested',
            OrderStatus::FINISHED => 'finished',
            OrderStatus::STARTED => 'started',
            default => 'unknown',
        };


        $object =  GetOrderableServiceObjectById::make()->handle($data->orderable_service_object_id);

        $startDate = Carbon::parse($data->start_time)->copy()->setTimezone($object->getObjectTimezone());
        $endDate = Carbon::parse($data->end_time)->copy()->setTimezone($object->getObjectTimezone());
        $order_date = $startDate->format('Y-m-d');


        $stay_period = $startDate->format('H:i') . ' - ' . $endDate->format('H:i');

        return [
            'booking_date' => $order_date,
            'booking_id' => $data->id,
            'object_name' => $object->name,
            'object_type' => $object->category->id,
            'status' => $status,
            'stay_period' => $stay_period,
        ];


    }
}
