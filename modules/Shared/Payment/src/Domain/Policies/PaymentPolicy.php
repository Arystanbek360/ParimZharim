<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\RolesAndPermissions\PaymentPermission;


class PaymentPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(PaymentPermission::VIEW_PAYMENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Payment $payment): ?bool
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

    public function update(User $user, Payment $payment): ?bool
    {
        return false;
    }

    public function replicate(User $user, Payment $payment): ?bool
    {
       return false;
    }

    public function delete(User $user, Payment $payment): ?bool
    {
        return false;
    }

    public function forceDelete(User $user, Payment $payment): ?bool
    {
        return false;
    }

    public function restore(User $user, Payment $payment): ?bool
    {
       return false;
    }

    public function cancelPayment(User $user, Payment $payment): ?bool
    {

        if ($payment->status !== PaymentStatus::CREATED && $payment->status !== PaymentStatus::PENDING) {
            return false;
        }
        if ($user->hasPermissionTo(PaymentPermission::MANAGE_PAYMENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function completePayment(User $user, Payment $payment): ?bool
    {

        if ($payment->status !== PaymentStatus::PENDING) {
            return false;
        }

        if ($user->hasPermissionTo(PaymentPermission::MANAGE_PAYMENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function markFailedPaymentShown(User $user, Payment $payment): ?bool
    {
        $metadata = is_string($payment->metadata) ? json_decode($payment->metadata, true) : $payment->metadata;


        if ($payment->status !== PaymentStatus::FAILED) {
            return false;
        }

        if (is_array($metadata) && isset($metadata['is_marked_as_shown']) && (bool) $metadata['is_marked_as_shown'] && $payment->status !== PaymentStatus::FAILED) {
            return false;
        }

        if ($user->hasPermissionTo(PaymentPermission::MANAGE_PAYMENT)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }
}
