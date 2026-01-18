<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Modules\Shared\Core\Application\BaseAction;
use Spatie\Permission\PermissionRegistrar;

class ForgetCachePermissionAndReloadOctane extends BaseAction
{
    /**
     * Handle the action.
     *
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $this->forgetCache();
        $this->reloadOctane();
    }

    /**
     * Forget cached permissions.
     *
     * @throws BindingResolutionException
     */
    private function forgetCache(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reload Octane server.
     */
    private function reloadOctane(): void
    {
        shell_exec('php artisan octane:reload --server=frankenphp 2>&1');
    }
}
