<?php declare(strict_types=1);


namespace Modules\Shared\Notification;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\Notification\Adapters\Cli\SendNotificationCommand;
use Modules\Shared\Notification\Domain\Repositories\NotifiableUserDeviceRepository;
use Modules\Shared\Notification\Domain\Repositories\NotificationRepository;
use Modules\Shared\Notification\Domain\Services\PushNotificationService;
use Modules\Shared\Notification\Infrastructure\Repositories\EloquentNotifiableUserDeviceRepository;
use Modules\Shared\Notification\Infrastructure\Repositories\EloquentNotificationRepository;
use Modules\Shared\Notification\Infrastructure\Services\FirebaseService;

class NotificationModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NotificationRepository::class, EloquentNotificationRepository::class);
        $this->app->bind(NotifiableUserDeviceRepository::class, EloquentNotifiableUserDeviceRepository::class);
        $this->app->bind(PushNotificationService::class, FirebaseService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->commands([
            SendNotificationCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(SendNotificationCommand::class)->everyMinute();
        });
    }
}
