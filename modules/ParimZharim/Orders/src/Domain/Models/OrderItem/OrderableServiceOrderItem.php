<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\OrderItem;

use Illuminate\Database\Eloquent\Builder;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableService;

/**
 * Class OrderableServiceOrderItem
 * @property OrderableService $orderable
 */
class OrderableServiceOrderItem extends OrderItem implements OrderItemInterface {

    protected $table = 'orders_order_items';

    protected $attributes = [
        'type' => OrderItemType::SERVICE->value, // Значение по умолчанию для поля 'type'
    ];


    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('service_order_item', function (Builder $builder) {
            $builder->where('type', '=', OrderItemType::SERVICE->value);
        });
    }

    public function calculatePrice(): float
    {
        if ($this->orderable) {
            return (float) $this->orderable->price;
        }
        return 0.0;
    }

    public function calculateTotal(): float
    {
        return $this->calculatePrice() * $this->quantity;
    }

    public function calculateDiscount(): float
    {
        return 0.0;
    }

    public function calculateTotalWithDiscount(): float
    {
        return $this->calculateTotal() - $this->calculateDiscount();
    }
}
