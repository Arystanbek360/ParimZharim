<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Policies;

use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTier;
use Modules\ParimZharim\LoyaltyProgram\Domain\RolesAndPermissions\LoyaltyProgramPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class DiscountTierPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::VIEW_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, DiscountTier $model): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::VIEW_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::MANAGE_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, DiscountTier $model): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::MANAGE_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, DiscountTier $model): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::MANAGE_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user, DiscountTier $model): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::MANAGE_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, DiscountTier $model): ?bool
    {
        return null;
    }

    public function restore(User $user, DiscountTier $model): ?bool
    {
        if ($user->hasPermissionTo(LoyaltyProgramPermission::MANAGE_LOYALTY_PROGRAM)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

}
