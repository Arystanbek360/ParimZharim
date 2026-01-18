<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\ParimZharim\Profile\Domain\Models\Employee;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Class OrderCreator full extends User
 */
class OrderCreator extends User {

    public function orders(): HasMany
    {
        // This should point to the correct related model and foreign key
        return $this->hasMany(Order::class, 'creator_id');
    }

    public function isEmployee(): bool {
        return $this->hasOne(Employee::class, 'user_id')->exists();
    }

    public function isCustomer(): bool {
        return !$this->isEmployee();
    }

    public function profile(): ?HasOne
    {
        if ($this->isEmployee()) {
            return $this->hasOne(Employee::class, 'user_id');
        }
        return $this->hasOne(Customer::class, 'user_id');
    }


}
