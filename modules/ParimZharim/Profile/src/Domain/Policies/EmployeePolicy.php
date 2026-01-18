<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Domain\Policies;

use Modules\ParimZharim\Profile\Domain\Models\Employee;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\ParimZharim\Profile\Domain\RolesAndPermissions\ProfilePermission;

class EmployeePolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::VIEW_EMPLOYEE_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::VIEW_EMPLOYEE_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Employee $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Employee $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }
    public function delete(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_PROFILES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function changePassword(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::CHANGE_EMPLOYEE_PASSWORD)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachRole(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function detachRole(User $user, Employee $model): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function attachAnyRole(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProfilePermission::MANAGE_EMPLOYEE_ROLES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
}
