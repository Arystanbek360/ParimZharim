<?php declare(strict_types=1);

namespace Modules\Shared\CMS;

use Livewire\Livewire;
use Modules\Shared\CMS\Adapters\Web\Components\DeleteAccountFormComponent;
use Modules\Shared\CMS\Adapters\Web\Components\PrivacyPolicyComponent;
use Modules\Shared\CMS\Domain\Repositories\ContentRepository;
use Modules\Shared\CMS\Infrastructure\Repositories\EloquentContentRepository;
use Modules\Shared\Core\BaseModuleProvider;

class CmsModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ContentRepository::class, EloquentContentRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../resources/css' => public_path('css/cms-module-assets'),
        ], 'cms-module-assets');


        Livewire::component('privacy-policy', PrivacyPolicyComponent::class);
        Livewire::component('delete-account-form', DeleteAccountFormComponent::class);
    }
}
