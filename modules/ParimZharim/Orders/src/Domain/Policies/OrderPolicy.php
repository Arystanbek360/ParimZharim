<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Policies;

use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableProductOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

class OrderPolicy extends BasePolicy
{

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Order $model): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::VIEW_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, Order $model): ?bool
    {
        if (in_array($model->status, [OrderStatus::CANCELLED, OrderStatus::COMPLETED, OrderStatus::FINISHED])) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Order $model): ?bool
    {
        if ($model->status != OrderStatus::CANCELLED) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function forceDelete(User $user, Order $model): ?bool
    {
        return false;
    }

    public function delete(User $user, Order $model): ?bool
    {
        return false;
    }

    public function restore(User $user, Order $model): ?bool
    {
        return false;
    }

    public function makeOrder(User $user): ?bool
    {
        return true;
    }

    public function cancelOrder(User $user, Order $model): ?bool
    {
        if (!in_array($model->status, [OrderStatus::CREATED, OrderStatus::CONFIRMED, OrderStatus::CANCELLATION_REQUESTED])) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        if ($model->customer?->user?->id == $user->id) {
            return true;
        }

        return null;
    }

    public function confirmOrder(User $user, Order $model): ?bool
    {
        if ($model->status != OrderStatus::CREATED) {
            return false;
        }

        if ($model->payments()->whereIn('status', [PaymentStatus::CREATED, PaymentStatus::SUCCESS, PaymentStatus::PENDING])->get()->count() > 0) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function finishOrder(User $user, Order $model): ?bool
    {
        if ($model->status != OrderStatus::STARTED) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function completeOrder(User $user, Order $model): ?bool
    {
        if ($model->status != OrderStatus::FINISHED) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function viewOrderByCustomer(User $user, Order $model): ?bool
    {
        if ($model->customer?->user?->id == $user->id) {
            return true;
        }

        return null;
    }

    public function syncOrderWithExternalSystem(User $user, Order $model): ?bool
    {
        $metadata = $model->metadata;

        if (isset($metadata['is_synced_in_external_system']) && $metadata['is_synced_in_external_system']) {
            return false;
        }

        // If the order status is not CONFIRMED and is_synced_in_external_system is true, return false
        if (!in_array($model->status, [OrderStatus::CONFIRMED, OrderStatus::CANCELLED])) {
            return false;
        }

        // Check if the user has permission to manage orders
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function recalculateOrder(User $user, Order $model): ?bool
    {

        if (in_array($model->status, [OrderStatus::CANCELLED, OrderStatus::COMPLETED, OrderStatus::CANCELLATION_REQUESTED])) {
            return false;
        }

        // Check if the user has permission to manage orders
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function applyDiscount (User $user, Order $model): ?bool
    {
        if (in_array($model->status, [OrderStatus::CANCELLED, OrderStatus::COMPLETED, OrderStatus::CANCELLATION_REQUESTED])) {
            return false;
        }

        // Check if the user has permission to manage orders
        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }

        return null;
    }

    public function addOrderableProductOrderItem(User $user, Order $model): ?bool
    {
        if (in_array($model->status, [OrderStatus::CANCELLED, OrderStatus::COMPLETED])) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }


    public function addOrderableServiceOrderItem(User $user, Order $model): ?bool
    {
        if (in_array($model->status, [OrderStatus::CANCELLED, OrderStatus::COMPLETED])) {
            return false;
        }

        if ($user->hasPermissionTo(OrderPermission::MANAGE_ORDERS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }



}
