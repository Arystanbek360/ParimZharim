<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\Policies;

use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\ParimZharim\Objects\Domain\RolesAndPermissions\ObjectPermission;

class ServiceObjectPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::VIEW_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, ServiceObject $model): ?bool
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

    public function update(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, ServiceObject $model): ?bool
    {
        return false;
    }

    public function delete(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachAnyTags(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachTags(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function detachTags(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, ServiceObject $model): ?bool
    {
        if ($user->hasPermissionTo(ObjectPermission::MANAGE_OBJECTS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

}
