<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentItem;
use Modules\Shared\Payment\Domain\RolesAndPermissions\PaymentPermission;


class PaymentItemPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(PaymentPermission::VIEW_PAYMENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, PaymentItem $paymentItem): ?bool
    {
        if ($user->hasPermissionTo(PaymentPermission::VIEW_PAYMENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        return false;
    }

    public function update(User $user, PaymentItem $paymentItem): ?bool
    {
        return false;
    }

    public function replicate(User $user, PaymentItem $paymentItem): ?bool
    {
       return false;
    }

    public function delete(User $user, PaymentItem $paymentItem): ?bool
    {
        return false;
    }

    public function forceDelete(User $user, PaymentItem $paymentItem): ?bool
    {
        return false;
    }

    public function restore(User $user, PaymentItem $paymentItem): ?bool
    {
       return false;
    }
}
