<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Models;


use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class Coupon
 *
 * @property int $id
 * @property int $creator_id
 * @property float $amount
 * @property array $metadata
 * @property CouponType $type
 * @property ?Carbon $valid_from
 * @property ?Carbon $valid_until
 * @property ?Carbon $used_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Coupon extends BaseModel {

    protected $table = 'loyalty_program_coupons';

    protected $fillable = [
        'creator_id',
        'amount',
        'metadata',
        'type',
        'valid_from',
        'valid_until',
        'used_at',
    ];

    protected $casts = [
        'type' => CouponType::class,
        'metadata' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'used_at' => 'datetime',
    ];


}
