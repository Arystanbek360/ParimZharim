<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile;

use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Infrastructure\Repositories\EloquentCustomerRepository;
use Modules\Shared\Core\BaseModuleProvider;

class ProfileModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
