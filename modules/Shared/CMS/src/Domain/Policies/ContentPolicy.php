<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Domain\Policies;

use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\CMS\Domain\RolesAndPermissions\ContentPermission;

class ContentPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::VIEW_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Content $content): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::VIEW_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user,  Content $content): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user,  Content $content): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user,  Content $content): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user,  Content $content): ?bool
    {
        return false; // return null to allow Super Admin access
    }

    public function restore(User $user,  Content $content): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }


}
