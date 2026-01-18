<?php declare(strict_types=1);

namespace Modules\Shared\Payment;

use Illuminate\Console\Scheduling\Schedule;
use Livewire\Livewire;
use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\Payment\Adapters\Cli\ProcessPaymentCommand;
use Modules\Shared\Payment\Adapters\Web\CardWidgetComponent;
use Modules\Shared\Payment\Domain\Repositories\PaymentMethodRepository;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Modules\Shared\Payment\Domain\Services\CloudPaymentServiceInterface;
use Modules\Shared\Payment\Infrastructure\Repositories\EloquentPaymentMethodRepository;
use Modules\Shared\Payment\Infrastructure\Repositories\EloquentPaymentRepository;
use Modules\Shared\Payment\Infrastructure\Services\TipTopPayPaymentService;

class PaymentModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentRepository::class, EloquentPaymentRepository::class);
        $this->app->bind(PaymentMethodRepository::class, EloquentPaymentMethodRepository::class);
        $this->app->bind(CloudPaymentServiceInterface::class, TipTopPayPaymentService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->commands([
            ProcessPaymentCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(ProcessPaymentCommand::class)->daily();
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'payment');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::component('payment-widget', CardWidgetComponent::class);
    }
}
