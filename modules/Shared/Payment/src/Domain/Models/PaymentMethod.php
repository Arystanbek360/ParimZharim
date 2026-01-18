<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Models;


use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class PaymentMethod
 * @property int $id
 * @property PaymentMethodType $type
 * @property bool $is_available_for_mobile
 * @property bool $is_available_for_web
 * @property bool $is_available_for_admin
 */
class PaymentMethod extends BaseModel {

    protected $table = 'payment_payment_methods';

    protected $fillable = [
        'id',
        'type',
        'is_available_for_mobile',
        'is_available_for_web',
        'is_available_for_admin',
    ];

    protected $casts = [
        'type' => PaymentMethodType::class,
        'is_available_for_mobile' => 'bool',
        'is_available_for_web' => 'bool',
        'is_available_for_admin' => 'bool',
    ];

}
