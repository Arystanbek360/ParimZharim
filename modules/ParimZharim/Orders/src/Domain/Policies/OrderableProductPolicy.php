<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableProduct;
use Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions\ProductsAndServicesPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class OrderableProductPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderableProduct $model): ?bool
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

    public function update(User $user, OrderableProduct $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, OrderableProduct $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, OrderableProduct $model): ?bool
    {
        return false;
    }

    public function delete(User $user, OrderableProduct $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, OrderableProduct $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
}
