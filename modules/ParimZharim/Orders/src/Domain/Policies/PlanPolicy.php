<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Plan;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class PlanPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Plan $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, Plan $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Plan $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, Plan $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user, Plan $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, Plan $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    //attach, detach object



}
