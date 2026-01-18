<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Schedule;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class SchedulePolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Schedule $model): ?bool
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

    public function update(User $user, Schedule $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Schedule $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, Schedule $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user, Schedule $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, Schedule $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    //attach detach Object
}
