<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Domain\Policies;

use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\ParimZharim\Profile\Domain\RolesAndPermissions\ProfilePermission;

class CustomerPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::VIEW_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Customer $model): ?bool
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

    public function update(User $user, Customer $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Customer $model): bool
    {
        return false;
    }

    public function delete(User $user, Customer $model): ?bool
    {
        return false;
    }

    public function forceDelete(User $user, Customer $model): ?bool
    {
        return false;
    }

    public function restore(User $user, Customer $model): ?bool
    {
       return false;
    }

    public function deleteCustomerProfile(User $user, Customer $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_CUSTOMER_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

}
