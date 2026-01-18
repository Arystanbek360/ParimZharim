<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\RolePermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;

class RolePolicy extends BasePolicy
{

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::VIEW_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Role $role): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::VIEW_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, Role $role): ?bool
    {
        // You can't update Super Admin Role
        if ($role->name === Roles::SUPER_ADMIN->value) {
            return false;
        }

        // You can't update Admin Role
        if ($role->name === Roles::ADMIN->value) {
            return false;
        }

        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Role $role): bool
    {
        return false;
    }

    public function delete(User $user, Role $role): ?bool
    {
        // You can't delete Super Admin Role
        if ($role->name === Roles::SUPER_ADMIN->value) {
            return false;
        }

        // You can't delete Admin Role
        if ($role->name === Roles::ADMIN->value) {
            return false;
        }

        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, Role $role): ?bool
    {
        return false;
    }

    public function restore(User $user, Role $role): ?bool
    {
        return false;
    }

    public function attachAnyPermission(User $user, Role $role): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachPermission(User $user, Role $role): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function detachPermission(User $user, Role $role): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function reloadSystem(User $user): ?bool
    {
        if ($user->hasPermissionTo(RolePermission::MANAGE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
}
