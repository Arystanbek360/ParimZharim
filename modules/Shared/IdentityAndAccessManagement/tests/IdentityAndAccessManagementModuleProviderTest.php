<?php

declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Modules\Shared\IdentityAndAccessManagement\IdentityAndAccessManagementModuleProvider;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentPersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentPhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentUserRepository;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Services\SmscSmsService;

class IdentityAndAccessManagementModuleProviderTest extends TestCase
{


    protected $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new IdentityAndAccessManagementModuleProvider($this->app);
    }

    public function testServiceBindings()
    {
        $this->provider->register();

        $this->assertInstanceOf(
            EloquentUserRepository::class,
            $this->app->make(UserRepository::class)
        );
        $this->assertInstanceOf(
            EloquentPhoneVerificationCodeRepository::class,
            $this->app->make(PhoneVerificationCodeRepository::class)
        );
        $this->assertInstanceOf(
            SmscSmsService::class,
            $this->app->make(SmsService::class)
        );
        $this->assertInstanceOf(
            EloquentPersonalAccessTokenRepository::class,
            $this->app->make(PersonalAccessTokenRepository::class)
        );
    }

    public function testBootstrappingServices()
    {
        $this->provider->boot();

        // Получаем список всех зарегистрированных команд
        $commands = \Illuminate\Support\Facades\Artisan::all();

        // Проверяем, что команда зарегистрирована, проверяя её сигнатуру
        $this->assertArrayHasKey(
            'idm:delete-old-phone-verification-codes',
            $commands,
            'CLI command to delete old phone verification codes should be registered and available.'
        );

        // Проверка наличия миграций
        $this->assertNotEmpty(
            $this->app['migrator']->paths(),
            'Migrations should be registered.'
        );

        // Проверяем наличие конкретных API маршрутов
        $this->assertNotEmpty(
            $this->app['router']->getRoutes()->match(app('request')->create('api/idm/request-phone-change-phone-code', 'POST'))->getActionName(),
            'API route for requesting phone change code should exist.'
        );
        $this->assertNotEmpty(
            $this->app['router']->getRoutes()->match(app('request')->create('api/idm/get-profile', 'GET'))->getActionName(),
            'API route for getting profile should exist.'
        );

        // Проверяем наличие веб-маршрута для входа
        $this->assertTrue(
            $this->app['router']->has('login'),
            'Web route for login should be registered.'
        );
    }

}
