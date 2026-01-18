<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Models;


use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class PaymentItem
 * @property int $id
 * @property int $payment_id
 * @property string $name
 * @property float $price
 * @property int $quantity
 * @property string $created_at
 * @property string $updated_at
 */
class PaymentItem extends BaseModel {

    protected $table = 'payment_payment_items';

    protected $fillable = [
        'payment_id',
        'name',
        'price',
        'quantity',
    ];
}
