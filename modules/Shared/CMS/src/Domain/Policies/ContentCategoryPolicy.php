<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Domain\Policies;

use Modules\Shared\CMS\Domain\Models\ContentCategory;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\CMS\Domain\RolesAndPermissions\ContentPermission;

class ContentCategoryPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::VIEW_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, ContentCategory   $categoryContent ): ?bool
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

    public function update(User $user,  ContentCategory   $categoryContent ): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user,  ContentCategory   $categoryContent ): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user,  ContentCategory   $categoryContent ): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user,  ContentCategory   $categoryContent ): ?bool
    {
        return false; // return null to allow Super Admin access
    }

    public function restore(User $user,  ContentCategory   $categoryContent ): ?bool
    {
        if ($user->hasPermissionTo(ContentPermission::MANAGE_CONTENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }


}
