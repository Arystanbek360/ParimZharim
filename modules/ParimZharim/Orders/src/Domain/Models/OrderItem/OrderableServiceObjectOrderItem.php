<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\OrderItem;

use Illuminate\Database\Eloquent\Builder;
use Modules\ParimZharim\Orders\Domain\Errors\PlanNotFound;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Services\OrderableServiceObjectPriceCalculationService;

/**
 * Class OrderableServiceObject full extends ServiceObject
 * @property OrderableServiceObject $orderable
 */
class OrderableServiceObjectOrderItem extends OrderItem implements OrderItemInterface
{
    protected $table = 'orders_order_items';

    protected $attributes = [
        'type' => OrderItemType::SERVICE_OBJECT->value, // Значение по умолчанию для поля 'type'
    ];

    // For Cache Purpose
    private float $cachedPrice = 0;

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('service_object_order_item', function (Builder $builder) {
            $builder->where('type', '=', OrderItemType::SERVICE_OBJECT->value);
        });
    }

    /**
     * @throws PlanNotFound
     */
    public function calculateTotal(): float
    {
        return $this->cachedPrice ?: $this->calculatePrice();
    }

    /**
     * @throws PlanNotFound
     */
    public function calculateDiscount(): float
    {
        $guests = $this->order->metadata['guests_adults'] + $this->order->metadata['guests_children'];
        $data =  OrderableServiceObjectPriceCalculationService::calculatePriceAndMetadata(
            $this->orderable,
            $this->order->start_time,
            $this->order->end_time,
            $this->order->customer,
            $guests,
            $this->order->coupon
        );
        return $data['discountPrice'];
    }

    /**
     * @throws PlanNotFound
     */
    public function calculateTotalWithDiscount(): float
    {
        return $this->calculateTotal() - $this->calculateDiscount();
    }

    /**
     * @throws PlanNotFound
     */
    public function calculatePrice(): float
    {
        $guests = $this->order->metadata['guests_adults'] + $this->order->metadata['guests_children'];
        $data  = OrderableServiceObjectPriceCalculationService::calculatePriceAndMetadata(
            $this->orderable,
            $this->order->start_time,
            $this->order->end_time,
            $this->order->customer,
            $guests,
            $this->order->coupon
        );
        $totalPrice = $data['totalObjectBookingPrice'];

        $this->cachedPrice = $totalPrice;
        $this->metadata = $data;

        return $totalPrice;
    }

    public function calculateAdvancePayment(): float
    {
        $guests = $this->order->metadata['guests_adults'] + $this->order->metadata['guests_children'];
        $data  = OrderableServiceObjectPriceCalculationService::calculatePriceAndMetadata(
            $this->orderable,
            $this->order->start_time,
            $this->order->end_time,
            $this->order->customer,
            $guests,
            $this->order->coupon
        );
        return $data['advancePayment'];
    }


}
