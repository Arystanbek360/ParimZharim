<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableProductOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class OrderableProductOrderItemPolicy extends BasePolicy
{

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderableProductOrderItem $model): ?bool
    {
        return false; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, OrderableProductOrderItem $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, OrderableProductOrderItem $model): ?bool
    {
        return false;
    }

    public function forceDelete(User $user, OrderableProductOrderItem $model): ?bool
    {
        return false;
    }

    public function delete(User $user, OrderableProductOrderItem $model): ?bool
    {
        if ($model->order->status == OrderStatus::CANCELLED || $model->order->status == OrderStatus::COMPLETED) {
          return false;
        }
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, OrderableProductOrderItem $model): ?bool
    {
        return false;
    }

}
