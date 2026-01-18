<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\OrderItem;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ParimZharim\Orders\Domain\Errors\UnknownOrderItemType;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableProduct;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableService;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class OrderItem represents an item in an order.
 *
 * @property int $id
 * @property int $order_id
 * @property int $orderable_id
 * @property OrderItemType $type
 * @property array $metadata
 * @property int $quantity
 * @property float $price
 * @property float $total
 * @property float $total_with_discount
 * @property Order $order
 */
class OrderItem extends BaseModel
{
    use SoftDeletes;

    protected $table = 'orders_order_items';

    protected $casts = [
        'type' => OrderItemType::class,
        'metadata' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (OrderItem $orderItem) {
            OrderService::calculateOrderItemsPrice($orderItem);
        });
        static::updating(function (OrderItem $orderItem) {
            OrderService::calculateOrderItemsPrice($orderItem);
        });
    }

    protected $fillable =
        [
            'order_id',
            'orderable_id',
            'quantity',
            'price',
            'total',
            'type',
            'metadata',
        ];


    public function orderable(): ?BelongsTo
    {
        return match ($this->type) {
            OrderItemType::SERVICE => $this->belongsTo(OrderableService::class, 'orderable_id'),
            OrderItemType::PRODUCT => $this->belongsTo(OrderableProduct::class, 'orderable_id'),
            OrderItemType::SERVICE_OBJECT => $this->belongsTo(OrderableServiceObject::class, 'orderable_id'),
            default => null,
        };
    }


    /**
     * @throws UnknownOrderItemType
     */
    public function newFromBuilder($attributes = [], $connection = null): OrderableServiceOrderItem|OrderableProductOrderItem|OrderableServiceObjectOrderItem
    {
        $type = $attributes->type;
        if (is_string($type)) {
            $type = OrderItemType::from($type);
        }
        $model = match ($type) {
            OrderItemType::PRODUCT => new OrderableProductOrderItem(),
            OrderItemType::SERVICE => new OrderableServiceOrderItem(),
            OrderItemType::SERVICE_OBJECT => new OrderableServiceObjectOrderItem(),
            default => throw new UnknownOrderItemType("Неизвестный тип позиции заказа:" . $type->value),
        };
        $model->exists = true;
        $model->setRawAttributes((array)$attributes, true);
        $model->setConnection($connection ?: $this->connection);
        return $model;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
