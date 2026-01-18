<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices;

use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceRepository;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentProductCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentProductRepository;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentServiceCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentServiceRepository;
use Modules\Shared\Core\BaseModuleProvider;

class ProductsAndServicesModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProductCategoryRepository::class, EloquentProductCategoryRepository::class);
        $this->app->bind(ServiceCategoryRepository::class, EloquentServiceCategoryRepository::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(ServiceRepository::class, EloquentServiceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
