<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\LoyaltyProgram\Database\Factories\DiscountTierFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Класс DiscountTier
 *
 * @property int $id
 * @property int $discount_percentage
 * @property float $threshold_amount
 * @property Carbon $start_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @package Modules\ParimZharim\LoyaltyProgram\Domain\Models
 */
class DiscountTier extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'loyalty_program_discount_tiers';

    protected $fillable = [
        'discount_percentage',
        'threshold_amount',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
    ];

    protected static function newFactory(): DiscountTierFactory
    {
        return DiscountTierFactory::new();
    }

}
