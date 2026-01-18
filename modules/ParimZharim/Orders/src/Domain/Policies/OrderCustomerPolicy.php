<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\OrderCustomer;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\ParimZharim\Profile\Domain\RolesAndPermissions\ProfilePermission;

class OrderCustomerPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::VIEW_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderCustomer $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::VIEW_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, OrderCustomer $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, OrderCustomer $model): bool
    {
        return false;
    }

    public function delete(User $user, OrderCustomer $model): ?bool
    {
        return false;
    }

    public function forceDelete(User $user, OrderCustomer $model): ?bool
    {
        return false;
    }

    public function restore(User $user, OrderCustomer $model): ?bool
    {
       return false;
    }

    public function deleteCustomerProfile(User $user, OrderCustomer $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

}
