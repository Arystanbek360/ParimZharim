<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram;

use Modules\ParimZharim\LoyaltyProgram\Domain\Repositories\LoyaltyProgramCustomerRepository;
use Modules\ParimZharim\LoyaltyProgram\Infrastructure\EloquentLoyaltyProgramCustomerRepository;
use Modules\Shared\Core\BaseModuleProvider;

class LoyaltyProgramModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(LoyaltyProgramCustomerRepository::class, EloquentLoyaltyProgramCustomerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
