<?php declare(strict_types=1);

namespace Modules\ParimZharim\WebApp;

use Modules\ParimZharim\WebApp\Adapters\Web\Components\Counter;
use Modules\Shared\Core\BaseModuleProvider;

class WebAppModuleProvider extends BaseModuleProvider
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'webapp');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../resources/css' => public_path('css'),
            __DIR__.'/../resources/js' => public_path('js'),
            __DIR__.'/../resources/img' => public_path('img'),
        ], 'parim-zharim-assets');

        \Livewire\Livewire::component('webapp-counter', Counter::class);
    }
}
