<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Policies;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions\ProductsAndServicesPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class ServicePolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Service $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, Service $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Service $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, Service $model): ?bool
    {
        return false;
    }

    public function delete(User $user, Service $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, Service $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }


}
