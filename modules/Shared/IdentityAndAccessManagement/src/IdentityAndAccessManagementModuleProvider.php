<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Cli\DeleteOldPhoneVerificationCodesConsoleCommand;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserDeviceRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentPersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentPhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentUserDeviceRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentUserRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Services\SmscSmsService;

class IdentityAndAccessManagementModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(PhoneVerificationCodeRepository::class, EloquentPhoneVerificationCodeRepository::class);
        $this->app->bind(SmsService::class, SmscSmsService::class);
        $this->app->bind(PersonalAccessTokenRepository::class, EloquentPersonalAccessTokenRepository::class);
        $this->app->bind(UserDeviceRepository::class, EloquentUserDeviceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->commands([
            DeleteOldPhoneVerificationCodesConsoleCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(DeleteOldPhoneVerificationCodesConsoleCommand::class)->daily();
        });
    }
}
