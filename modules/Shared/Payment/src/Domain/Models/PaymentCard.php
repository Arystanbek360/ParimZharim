<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Models;

use Modules\Shared\Core\Domain\BaseModel;

/**
 * @property int $id
 * @property int $customer_id
 * @property ?int $payment_id
 * @property string $token
 * @property ?string $card_mask
 * @property ?string $card_first_six
 * @property ?string $card_last_four
 * @property ?int $expiration_date_month
 * @property ?int $expiration_date_year
 * @property ?string $card_exp_date
 * @property ?string $card_type
 * @property ?string $issuer
 * @property ?string $gateway_name
 * @property bool $is_active
 */
class PaymentCard extends BaseModel
{
    protected $table = 'payment_cards';

    protected $fillable = [
        'customer_id',
        'payment_id',
        'token',
        'card_mask',
        'card_first_six',
        'card_last_four',
        'expiration_date_month',
        'expiration_date_year',
        'card_exp_date',
        'card_type',
        'issuer',
        'gateway_name',
        'is_active',
    ];

    protected $casts = [
        'expiration_date_month' => 'integer',
        'expiration_date_year' => 'integer',
        'is_active' => 'boolean',
    ];
}
