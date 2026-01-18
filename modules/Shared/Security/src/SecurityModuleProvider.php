<?php declare(strict_types=1);


namespace Modules\Shared\Security;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\Security\Domain\Policies\ActionEventPolicy;

class SecurityModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
       // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
      //  $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
      Gate::policy(ActionEvent::class, ActionEventPolicy::class);
    }
}
