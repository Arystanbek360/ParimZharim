<?php declare(strict_types=1);


namespace Modules\Shared\Security\Domain\Policies;

use Laravel\Nova\Actions\ActionEvent;
use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Security\Domain\RolesAndPermissions\ActionEventPermission;

class ActionEventPolicy extends BasePolicy
{

    public function viewAny(User $user): ?bool
    {
       if( $user->hasPermissionTo(ActionEventPermission::VIEW_ACTION_EVENT)){
           return true;
       }

       return null;
    }

    public function view(User $user, ActionEvent $model): ?bool
    {
        if( $user->hasPermissionTo(ActionEventPermission::VIEW_ACTION_EVENT)){
            return true;
        }

        return null;
    }
}
