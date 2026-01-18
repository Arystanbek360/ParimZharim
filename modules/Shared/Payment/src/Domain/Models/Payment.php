<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class Payment
 *
 * @property int $id
 * @property int $payable_order_id
 * @property int $customer_id
 * @property PaymentStatus $status
 * @property PaymentItemCollection $items
 * @property float $total
 * @property PaymentMethodType $payment_method
 * @property array $metadata
 * @property ?string $external_id
 * @property string $comment
 * @property string $created_at
 * @property string $updated_at
 */

class Payment extends BaseModel {

    protected $table = 'payment_payments';

    protected $fillable = [
        'payable_order_id',
        'customer_id',
        'status',
        'total',
        'items',
        'payment_method',
        'external_id',
        'comment',
        'metadata',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'payment_method' => PaymentMethodType::class,
        'items' => PaymentItemCollection::class,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PaymentItem::class, 'payment_id');
    }


    public function getIsMarkedAsShownAttribute(): bool
    {
        return isset($this->metadata['is_marked_as_shown']) && $this->metadata['is_marked_as_shown'];
    }

}
