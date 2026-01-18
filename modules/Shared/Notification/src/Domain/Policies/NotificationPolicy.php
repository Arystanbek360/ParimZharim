<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Models\Notification;
use Modules\Shared\Notification\Domain\RolesAndPermissions\NotificationPermission;

class NotificationPolicy extends BasePolicy
{
    public function viewAny(User $user): ?bool
    {
        if ($user->hasPermissionTo(NotificationPermission::VIEW_NOTIFICATIONS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function view(User $user, Notification $notification): ?bool
    {
        if ($user->hasPermissionTo(NotificationPermission::VIEW_NOTIFICATIONS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function create(User $user): ?bool
    {
        if ($user->hasPermissionTo(NotificationPermission::MANAGE_NOTIFICATIONS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function update(User $user,  Notification $notification): ?bool
    {
        if (!$this->isUpdateAllowed($user, $notification)) {
            return false;
        }
        if ($user->hasPermissionTo(NotificationPermission::MANAGE_NOTIFICATIONS) && $this->isUpdateAllowed($user, $notification)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function replicate(User $user,  Notification $notification): ?bool
    {
        if ($user->hasPermissionTo(NotificationPermission::MANAGE_NOTIFICATIONS)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function delete(User $user,  Notification $notification): ?bool
    {
        if (!$this->isUpdateAllowed($user, $notification)) {
            return false;
        }
        if ($user->hasPermissionTo(NotificationPermission::MANAGE_NOTIFICATIONS) && $this->isUpdateAllowed($user, $notification)) {
            return true;
        }
        return null; // return null to allow Super Admin access
    }

    public function forceDelete(User $user, Notification $notification): ?bool
    {
        return null; // return null to allow Super Admin access
    }

    public function restore(User $user,  Notification $notification): ?bool
    {
        return null;
    }

    private function isUpdateAllowed(User $user, Notification $notification): bool
    {
        return $notification->planed_send_at > now();
    }

}
