<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableService;
use Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions\ProductsAndServicesPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class OrderableServicePolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderableService $model): ?bool
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

    public function update(User $user, OrderableService $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, OrderableService $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, OrderableService $model): ?bool
    {
        return false;
    }

    public function delete(User $user, OrderableService $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, OrderableService $model): ?bool
    {
        if ($user->hasPermissionTo(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }


}
