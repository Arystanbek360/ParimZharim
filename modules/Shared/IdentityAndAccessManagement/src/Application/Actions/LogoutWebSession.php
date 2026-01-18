<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class LogoutWebSession extends BaseAction {
    public function handle(User $user): void {
        Auth::logout();
    }
}
