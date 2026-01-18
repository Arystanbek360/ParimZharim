<?php

namespace App\Providers;

use App\Nova\Overrides\Http\Controllers\Pages\OverrideResourceDetailController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Controllers\Pages\ResourceDetailController;
use Laravel\Sanctum\Sanctum;
use Modules\ParimZharim\LoyaltyProgram\Domain\RolesAndPermissions\LoyaltyProgramPermission;
use Modules\ParimZharim\Objects\Domain\RolesAndPermissions\ObjectPermission;
use Modules\ParimZharim\Orders\Domain\RolesAndPermissions\OrderPermission;
use Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions\ProductsAndServicesPermission;
use Modules\ParimZharim\Profile\Domain\RolesAndPermissions\ProfilePermission;
use Modules\Shared\CMS\Domain\RolesAndPermissions\ContentPermission;
use Modules\Shared\Core\Domain\DomainServices\PlatformSettingsService;
use Modules\Shared\Core\Domain\Models\TypeModelResolver;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PersonalAccessToken;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\EnumResolver as RolesAndPermissionsEnumResolver;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\PermissionPermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\PhoneVerificationCodePermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\RolePermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\UserPermission;
use Modules\Shared\Notification\Domain\RolesAndPermissions\NotificationPermission;
use Modules\Shared\Payment\Domain\RolesAndPermissions\PaymentPermission;
use Modules\Shared\Security\Domain\RolesAndPermissions\ActionEventPermission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow super admin to access all actions that returns null in the Policy class
        Gate::after(function (User $user, $ability) {
            return $user->hasRole(Roles::SUPER_ADMIN);
        });

        // Use own PersonalAccessToken model
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);


        $this->app->bind(RolesAndPermissionsEnumResolver::ENUM_RESOLVER_CONTAINER, function($app) {
            return [
                PermissionPermission::class,
                RolePermission::class,
                UserPermission::class,
                PhoneVerificationCodePermission::class,
                ContentPermission::class,
                ActionEventPermission::class,
                PaymentPermission::class,
                ObjectPermission::class,
                ProductsAndServicesPermission::class,
                OrderPermission::class,
                ProfilePermission::class,
                LoyaltyProgramPermission::class,
                NotificationPermission::class
                // Добавьте сюда другие enum классы
            ];
        });

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        $this->app->bind(ResourceDetailController::class, OverrideResourceDetailController::class);

        $this->app->booted(function () {
            $platformSettingsService = $this->app->make(PlatformSettingsService::class);
            $platformSettingsService->lockSettings();

            // Now that settings are locked, register TypeModelResolver
            $this->app->singleton(TypeModelResolver::class, function ($app) use ($platformSettingsService) {
                return new TypeModelResolver($platformSettingsService);
            });
        });

    }
}
