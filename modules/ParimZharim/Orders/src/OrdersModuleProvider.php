<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Modules\ParimZharim\Orders\Adapters\Bus\OrderStatusUpdateListener;
use Modules\ParimZharim\Orders\Adapters\Cli\CreateNotificationForOrdersCommand;
use Modules\ParimZharim\Orders\Adapters\Cli\ProceedOrderLifecycleConsoleCommand;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\ParimZharim\Orders\Infrastructure\Repositories\EloquentOrderableServiceObjectRepository;
use Modules\ParimZharim\Orders\Infrastructure\Repositories\EloquentOrderRepository;
use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\Payment\Domain\Events\PaymentFailed;
use Modules\Shared\Payment\Domain\Events\PaymentSucceeded;

class OrdersModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
        $this->app->bind(OrderableServiceObjectRepository::class, EloquentOrderableServiceObjectRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->commands([
            ProceedOrderLifecycleConsoleCommand::class,
            CreateNotificationForOrdersCommand::class
        ]);

        Event::listen(
            PaymentSucceeded::class,
            [OrderStatusUpdateListener::class, 'handle']
        );

        Event::listen(
            PaymentFailed::class,
            [OrderStatusUpdateListener::class, 'handle']
        );

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(ProceedOrderLifecycleConsoleCommand::class)->everyMinute();
            $schedule->command(CreateNotificationForOrdersCommand::class)->everyMinute();
        });
    }
}
