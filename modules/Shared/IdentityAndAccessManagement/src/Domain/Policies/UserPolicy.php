<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\UserPermission;

class UserPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::VIEW_USERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, User $model): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::VIEW_USERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::MANAGE_USERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, User $model): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::MANAGE_USERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, User $model): bool
    {
        return false;
    }

    public function delete(User $user, User $model): ?bool
    {
       return false;
    }

    public function forceDelete(User $user, User $model): ?bool
    {
        return false;
    }

    public function restore(User $user, User $model): ?bool
    {
        return false;

    }

    public function attachAnyRole(User $user, User $model): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::MANAGE_USER_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachRole(User $user, User $model): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::MANAGE_USER_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function detachRole(User $user, User $model): ?bool
    {
        if ($user->hasPermissionTo(UserPermission::MANAGE_USER_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
}
