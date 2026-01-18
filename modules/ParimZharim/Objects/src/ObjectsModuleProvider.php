<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects;

use Modules\ParimZharim\Objects\Domain\Repositories\CategoryRepository;
use Modules\ParimZharim\Objects\Domain\Repositories\TagRepository;
use Modules\ParimZharim\Objects\Infrastructure\Repositories\EloquentCategoryRepository;
use Modules\ParimZharim\Objects\Infrastructure\Repositories\EloquentTagRepository;
use Modules\Shared\Core\BaseModuleProvider;
use Illuminate\Support\Facades\Log;

class ObjectsModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
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
