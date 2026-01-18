<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Payment\Domain\Models\PaymentMethod;


class PaymentMethodPolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, PaymentMethod $paymentMethod): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        return false;
    }

    public function update(User $user, PaymentMethod $paymentMethod): ?bool
    {
        return null;
    }

    public function replicate(User $user, PaymentMethod $paymentMethod): ?bool
    {
       return false;
    }

    public function delete(User $user, PaymentMethod $paymentMethod): ?bool
    {
        return false;
    }

    public function forceDelete(User $user,PaymentMethod $paymentMethod): ?bool
    {
        return false;
    }

    public function restore(User $user, PaymentMethod $paymentMethod): ?bool
    {
       return false;
    }
}
