<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate;

use Modules\Shared\Core\BaseModuleProvider;

class ModuleTemplateModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
