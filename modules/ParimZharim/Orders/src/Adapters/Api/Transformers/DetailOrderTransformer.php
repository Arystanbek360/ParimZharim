<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Api\Transformers;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Application\Actions\GetOrderableServiceObjectByID;
use Modules\ParimZharim\Orders\Application\Actions\GetScheduleDetailsForObjectOnDate;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemType;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;
use Illuminate\Support\Facades\Storage;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

class DetailOrderTransformer extends BaseTransformer
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

        $object = GetOrderableServiceObjectById::make()->handle($data->orderable_service_object_id);
        $scheduleDetails = GetScheduleDetailsForObjectOnDate::make()->handle($object, $data->start_time);

        $startDate = Carbon::parse($data->start_time)->copy()->setTimezone($object->getObjectTimezone());
        $endDate = Carbon::parse($data->end_time)->copy()->setTimezone($object->getObjectTimezone());
        $date = [
            'booking_date' =>$startDate->format('Y-m-d'),
            'check_in' => $startDate->format('H:i:s'),
            'hours' => $endDate->diff($startDate)->h,
        ];

        $orderItems = $data->orderItems;
        $products = [];
        $additionalServices = [];
        $fines = [];
        foreach ($orderItems as $orderItem) {
            if ($orderItem->type == OrderItemType::PRODUCT) {
                $products[] = [
                    'product_id' => $orderItem->orderable_id,
                    'product_quantity' => $orderItem->quantity,
                    'product_info' => [
                        'name' => $orderItem->orderable?->name,
                        'description' => $orderItem->orderable?->description,
                        'photo' => $orderItem->orderable?->image ? asset(Storage::disk(config('filesystems.default'))->url($orderItem->orderable?->image)) : null,
                        'price' => (float) $orderItem->price,
                    ],
                ];
            } elseif ($orderItem->type == OrderItemType::SERVICE && $orderItem->orderable?->service_category_id !=1) {
                $additionalServices[] = [
                    'service_id' => $orderItem->orderable_id,
                    'quantity' => $orderItem->quantity,
                    'service_info' => [
                        'name' => $orderItem->orderable?->name,
                        'price' => (float) $orderItem->price,
                    ],
                ];
                //category 1 is fines
            } elseif ($orderItem->type == OrderItemType::SERVICE && $orderItem->orderable?->service_category_id == 1) {
                $fines[] = [
                    'id' => $orderItem->orderable_id,
                    'quantity' => $orderItem->quantity,
                    'name' => $orderItem->orderable?->name,
                    'price' => (float) $orderItem->price,
                ];
            }
        }

        $paymentMethod = null;
        if ($data->payments() && $data->payments()->count() > 0) {
            $payment = $data->payments()->first();
            $paymentMethod = $payment->payment_method;
        } else if (isset($data->metadata['paymentMethod'])) {
            $paymentMethod = $data->metadata['paymentMethod'];
        } else {
            $paymentMethod = null;
        }

        $transformedPayments = $this->transformPayments(new PaymentCollection($data->payments()->get()));

        return [
            'date' => $date,
            'booking_id' => $data->id,
            'object_name' => $object->name,
            'object_id' => $object->id,
            'status' => $status,
            'persons' => [
                'adults' => (int) $data->metadata['guests_adults'] ?? 0,
                'kids' => (int) $data->metadata['guests_children'] ?? 0,
            ],
            "payment_method" => $paymentMethod,
            "total" => (float) $data->total_with_discount,
            "advance_payment" => (float) $data->metadata['expectedAdvancePayment'] ?? 0,
            "products" => $products,
            "additional_services" => $additionalServices,
            "fines" => $fines,
            "notes" => $data->metadata['customerNotes'] ?? null,
            "payments" => $transformedPayments,
            "confirmation_waiting_duration" => $scheduleDetails['confirmation_waiting_duration'],
        ];
    }

    private function transformPayments(PaymentCollection $payments): array
    {
        $transformed = [];
        foreach ($payments as $payment) {
            if ($payment->status === PaymentStatus::FAILED || $payment->status === PaymentStatus::CANCELED) {
                continue;
            }

            $url = null;
            if ($payment->payment_method == PaymentMethodType::CLOUD_PAYMENT && $payment->status == PaymentStatus::CREATED) {
                $url = config('app.url') . '/payment-widget/' . $payment->id;
            }

            $transformed[] = [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => (float) $payment->total,
                'url' => $url,
            ];
        }
        return $transformed;
    }
}
