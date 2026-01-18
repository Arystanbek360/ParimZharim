<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderCreator;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class OrderCreatorPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderCreator $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        return false;
    }

    public function update(User $user, OrderCreator $model): ?bool
    {
        return false;
    }

    public function replicate(User $user, OrderCreator $model): ?bool
    {
       return false;
    }

    public function forceDelete(User $user, OrderCreator $model): ?bool
    {
        return false;
    }

    public function delete(User $user, OrderCreator $model): ?bool
    {
        return false;
    }

    public function restore(User $user, OrderCreator $model): ?bool
    {
        return false;
    }
}
