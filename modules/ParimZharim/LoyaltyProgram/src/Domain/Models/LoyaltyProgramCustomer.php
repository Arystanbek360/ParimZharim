<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Models;


use Modules\ParimZharim\LoyaltyProgram\Database\Factories\LoyaltyProgramCustomerFactory;
use Modules\ParimZharim\Profile\Domain\Models\Customer;

/**
 * Class LoyaltyProgramCustomer
 *
 * @property int $discount
 *
 */
class LoyaltyProgramCustomer extends Customer {

    protected $table = 'profile_customers';

    protected $fillable = [
        'discount'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (is_null($model->discount)) {
                $model->discount = 0; // Default fallback
            }
        });
    }
    protected static function newFactory(): LoyaltyProgramCustomerFactory
    {
        return LoyaltyProgramCustomerFactory::new();
    }
}
