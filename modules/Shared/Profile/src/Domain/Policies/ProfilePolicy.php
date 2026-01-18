<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Models\Profile;

class ProfilePolicy extends BasePolicy {

    public function viewAny(User $user): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Profile $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function update(User $user, Profile $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user, Profile $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Profile $model): ?bool
    {
        return false; // return null to allow Super Admin access
    }
    public function delete(User $user, Profile $model): ?bool
    {
        if ($model->email == 'admin@arista.kz') {
            return false;
        }

        if ($user->id == $model->user_id) {
            return false;
        }

        return null; // return null to allow Super Admin access
    }

    public function restore(User $user, Profile $model): ?bool
    {
        return null; // return null to allow Super Admin access
    }

}
