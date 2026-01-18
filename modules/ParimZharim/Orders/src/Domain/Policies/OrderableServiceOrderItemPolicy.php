<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class OrderableServiceOrderItemPolicy extends BasePolicy
{

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, OrderableServiceOrderItem $model): ?bool
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

    public function update(User $user, OrderableServiceOrderItem $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, OrderableServiceOrderItem $model): ?bool
    {
        return false;
    }

    public function forceDelete(User $user, OrderableServiceOrderItem $model): ?bool
    {
        return false;
    }

    public function delete(User $user, OrderableServiceOrderItem $model): ?bool
    {
        if ($model->order->status == OrderStatus::CANCELLED || $model->order->status == OrderStatus::COMPLETED) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, OrderableServiceOrderItem $model): ?bool
    {
        return false;
    }

}
