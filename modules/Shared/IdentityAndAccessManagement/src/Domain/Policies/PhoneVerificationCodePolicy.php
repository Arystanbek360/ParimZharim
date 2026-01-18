<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\PhoneVerificationCodePermission;

class PhoneVerificationCodePolicy extends BasePolicy
{
    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(PhoneVerificationCodePermission::VIEW_PHONE_VERIFICATION_CODES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, PhoneVerificationCode $model): ?bool
    {
        if ($user->hasPermissionTo(PhoneVerificationCodePermission::VIEW_PHONE_VERIFICATION_CODES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function managePhoneVerificationCode(User $user, PhoneVerificationCode $model): ?bool
    {
        if ($user->hasPermissionTo(PhoneVerificationCodePermission::MANAGE_PHONE_VERIFICATION_CODES)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function update(User $user, PhoneVerificationCode $model): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function replicate(User $user, PhoneVerificationCode $model): bool
    {
        return false; // permissions are managed by the system
    }

    public function delete(User $user, PhoneVerificationCode $model): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function forceDelete(User $user, PhoneVerificationCode $model): ?bool
    {
        return false; // permissions are managed by the system
    }

    public function restore(User $user, PhoneVerificationCode $model): ?bool
    {
        return false; // permissions are managed by the system
    }
}
