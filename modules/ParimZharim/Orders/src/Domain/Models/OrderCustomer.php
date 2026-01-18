<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;

/**
 * Class OrderCustomer full extends Customer
 */
class OrderCustomer extends LoyaltyProgramCustomer {

    protected $table = 'profile_customers';

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

}
