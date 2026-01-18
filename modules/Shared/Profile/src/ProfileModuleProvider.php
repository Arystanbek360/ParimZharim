<?php declare(strict_types=1);

namespace Modules\Shared\Profile;

use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;
use Modules\Shared\Profile\Infrastructure\Repositories\EloquentProfileRepository;

class ProfileModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProfileRepository::class, EloquentProfileRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
