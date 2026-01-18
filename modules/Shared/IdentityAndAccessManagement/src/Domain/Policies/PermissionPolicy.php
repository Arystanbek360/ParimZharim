<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\PermissionPermission;

class PermissionPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(PermissionPermission::VIEW_PERMISSIONS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Permission $permission): ?bool
    {
        return false;
    }

    public function create(User $user): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function update(User $user, Permission $permission): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function replicate(User $user, Permission $permission): bool
    {
        return false; // permissions are managed by the system
    }

    public function delete(User $user, Permission $permission): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function forceDelete(User $user, Permission $permission): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function restore(User $user, Permission $permission): ?bool
    {
        return false; // permissions are managed by the system
    }
}
