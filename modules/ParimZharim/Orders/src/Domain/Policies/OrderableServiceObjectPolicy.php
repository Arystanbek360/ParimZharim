<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Objects\Domain\RolesAndPermissions\ObjectPermission;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class OrderableServiceObjectPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::VIEW_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::VIEW_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, OrderableServiceObject $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, OrderableServiceObject $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachTags(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function detachTags(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachPlan(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
    public function attachAnyPlan(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function detachPlan(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachSchedule(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachAnySchedule(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }


    public function detachSchedule(User $user, OrderableServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
}
