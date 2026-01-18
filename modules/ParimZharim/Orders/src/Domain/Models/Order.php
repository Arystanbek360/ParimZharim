<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\Coupon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableProductOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceObjectOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemType;
use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

/**
 * Class Order
 * @property int $id
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property Carbon $confirm_before
 * @property float $total
 * @property float $total_with_discount
 * @property int $creator_id
 * @property int $customer_id
 * @property int $orderable_service_object_id
 * @property array $metadata
 * @property OrderableServiceObject $orderableServiceObject
 * @property OrderCustomer $customer
 * @property OrderCreator $creator
 * @property OrderStatus $status
 * @property OrderItemCollection $orderItems
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Coupon $coupon
 */
class Order extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders_orders';

    protected $fillable = [
        'orderable_service_object_id',
        'customer_id',
        'start_time',
        'end_time',
        'creator_id',
        'metadata',
        'status',
        'confirm_before'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total' => 'float',
        'metadata' => 'array',
        'confirm_before' => 'datetime',
        'status' => OrderStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(OrderCustomer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(OrderCreator::class);
    }

    public function orderableServiceObject(): BelongsTo
    {
        return $this->belongsTo(OrderableServiceObject::class);
    }

    public function getTotalAttribute(): float
    {
        $total = 0.0;
        if ($this->orderItems) {
            foreach ($this->orderItems as $orderItem) {
                $total += $orderItem->total;
            }
        }
        return $total;
    }

    public function getTotalWithDiscountAttribute(): float
    {
        $total = 0.0;
        if ($this->orderItems) {
            foreach ($this->orderItems as $orderItem) {
                $total += $orderItem->total_with_discount;
            }
        }
        return $total;
    }

    public static function boot(): void
    {
        parent::boot();
        static::creating(function (Order $order) {
            OrderService::validateOrderParams($order);
            OrderService::writeInitialOrderData($order);
            OrderService::writeDefaultOrderSource($order);
        });
        static::created(function (Order $order) {
            OrderService::createOrderableServiceObjectOrderItemIfNotExist($order);
        });

        static::updating(function (Order $order) {
            OrderService::performRequiredCalculationsOnOrderDataUpdate($order);
        });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productOrderItems(): HasMany
    {
        return $this->hasMany(OrderableProductOrderItem::class);
    }

    public function serviceOrderItems(): HasMany
    {
        return $this->hasMany(OrderableServiceOrderItem::class);
    }

    public function serviceObjectOrderItems(): HasMany
    {
        return $this->hasMany(OrderableServiceObjectOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payable_order_id');
    }

    public function getProductOrderItemsTotalAttribute(): float
    {
        $total = 0.0;
        /** @var OrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            if ($orderItem->type === OrderItemType::PRODUCT) {
                $total += (float)$orderItem->total;
            }
        }
        return $total;
    }

    public function getServiceOrderItemsTotalAttribute(): float
    {
        $total = 0.0;
        /** @var OrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            if ($orderItem->type === OrderItemType::SERVICE) {
                $total += (float)$orderItem->total;
            }
        }
        return $total;
    }

    public function getServiceObjectOrderItemsTotalAttribute(): float
    {
        $total = 0.0;
        /** @var OrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            if ($orderItem->type === OrderItemType::SERVICE_OBJECT) {
                $total += (float)$orderItem->total;
            }
        }
        return $total;
    }

    public function getServiceObjectOrderItemsTotalWithDiscountAttribute(): float
    {
        $total = 0.0;
        /** @var OrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            if ($orderItem->type === OrderItemType::SERVICE_OBJECT) {
                $total += (float)$orderItem->total_with_discount;
            }
        }
        return $total;
    }

    public function getTotalToPayAttribute(): float
    {
        $advancePayment = OrderService::getActualAdvancePayment($this);
        return (float)($this->total_with_discount - $advancePayment);
    }

    // Attributes for download Excel file
    public function getGuestsAdultsAttribute()
    {
        return $this->metadata['guests_adults'] ?? null;
    }

    public function getGuestsChildrenAttribute()
    {
        return $this->metadata['guests_children'] ?? 0;
    }

    public function getNotesAttribute()
    {
        return $this->metadata['notes'] ?? '';
    }

    public function getCustomerNotesAttribute()
    {
        return $this->metadata['customerNotes'] ?? '';
    }

    public function getIsSyncedInExternalSystemAttribute(): string
    {
        if (!isset($this->metadata['is_synced_in_external_system'])) {
            return 'Нет';
        }
        return $this->metadata['is_synced_in_external_system'] ? 'Да' : 'Нет';
    }

    public function getActualAdvancePaymentAttribute(): float
    {
        return OrderService::getActualAdvancePayment($this);
    }

    public function getExpectedAdvancePaymentAttribute(): float
    {
        if (!isset($this->metadata['expectedAdvancePayment'])) {
            return 0;
        }
        return (float)$this->metadata['expectedAdvancePayment'] ?? 0;
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->orderableServiceObject->category->name ?? '';
    }

    public function getOrderStatusAttribute(): string
    {
        return $this->status->label();
    }

    public function getCustomerDiscountAttribute(): float
    {
        return $this->metadata['order_discount'] ?? 0;
    }

    public function getCouponAttribute(): ?Coupon
    {
        // Check if 'coupon' key exists and is non-empty
        if (empty($this->metadata['coupon'])) {
            return null;
        }

        // Check if both 'id' and 'amount' keys exist in the 'coupon' array
        if (!isset($this->metadata['coupon']['id']) || !isset($this->metadata['coupon']['amount'])) {
            return null;  // Optionally, handle or log an error if expected keys are missing
        }

        // Assuming all checks are passed, return a new Coupon object
        return new Coupon([
            'id' => $this->metadata['coupon']['id'],
            'amount' => $this->metadata['coupon']['amount']
        ]);
    }

    public function setCouponAttribute(?Coupon $coupon): void
    {
        $metadata = $this->metadata;
        if ($coupon) {
            $metadata['coupon'] = [
                'id' => $coupon->id,
                'amount' => $coupon->amount,
            ];
        } else {
            unset($metadata['coupon']);
        }
        $this->metadata = $metadata;
    }

    public function getOrderStartTimeAttribute(): string
    {
        Carbon::setLocale('ru');
        $start_time = $this->start_time->setTimezone('Asia/Almaty');
        return $start_time->isoFormat('dd,  D MMMM YYYY HH:mm');
    }

    public function getOrderEndTimeAttribute(): string
    {
        Carbon::setLocale('ru');
        $end_time = $this->end_time->setTimezone('Asia/Almaty');
        return $end_time->isoFormat('dd,  D MMMM YYYY HH:mm');
    }

    public function getPaymentMethodAttribute(): string
    {
        $payment = $this->payments()->get()->filter(function ($payment) {
            return in_array($payment->status, [PaymentStatus::CREATED, PaymentStatus::SUCCESS, PaymentStatus::COMPLETED, PaymentStatus::PENDING]);
        })->first();

        if ($payment) {
            return $payment->payment_method->value;
        } else {
            return $this->metadata['paymentMethod'] ?? '';
        }
    }
}
