<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RegisterUserByPhoneIfNotExists;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class RegisterUserByPhoneIfNotExistsTest extends TestCase
{
    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     Log::info('Setting up test');
    // }

    // /**
    //  * @runInSeparateProcess
    //  */
    // public function testHandleRegistersUserIfNotExists(): void
    // {
    //     Log::info('Starting testHandleRegistersUserIfNotExists');

    //     $data = new UserProfileData(
    //         phone: '+1234567890',
    //         name: 'Test User',
    //         email: 'test@example.com',
    //         password: 'password'
    //     );

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $userRepositoryMock->shouldReceive('findByPhone')
    //         ->once()
    //         ->with($data->phone)
    //         ->andReturnNull();
    //     $userRepositoryMock->shouldReceive('save')
    //         ->once()
    //         ->with(Mockery::type(User::class))
    //         ->andReturn(true); // Убедитесь, что save возвращает true

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validatorMock = Mockery::mock('alias:Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($data->phone)
    //         ->andReturnTrue();

    //     $this->app->instance(ValidateAuthenticationPhoneNumber::class, $validatorMock);

    //     $action = new RegisterUserByPhoneIfNotExists($userRepositoryMock);

    //     try {
    //         $action->handle($data);
    //     } catch (\Exception $e) {
    //         Log::error('Exception in testHandleRegistersUserIfNotExists', ['exception' => $e]);
    //         throw $e;
    //     }

    //     $this->assertTrue(true);

    //     Log::info('testHandleRegistersUserIfNotExists completed');
    // }

    // /**
    //  * @runInSeparateProcess
    //  */
    // public function testHandleDoesNotRegisterUserIfExists(): void
    // {
    //     Log::info('Starting testHandleDoesNotRegisterUserIfExists');

    //     $data = new UserProfileData(
    //         phone: '+1234567890',
    //         name: 'Test User',
    //         email: 'test@example.com',
    //         password: 'password'
    //     );

    //     $existingUser = Mockery::mock(User::class);
    //     $existingUser->shouldReceive('jsonSerialize')
    //         ->andReturn([
    //             'phone' => $data->phone,
    //             'name' => 'Existing User',
    //             'email' => 'existing@example.com'
    //         ]);

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $userRepositoryMock->shouldReceive('findByPhone')
    //         ->once()
    //         ->with($data->phone)
    //         ->andReturn($existingUser);
    //     $userRepositoryMock->shouldNotReceive('save');

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validatorMock = Mockery::mock('alias:Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($data->phone)
    //         ->andReturnTrue();

    //     $this->app->instance(ValidateAuthenticationPhoneNumber::class, $validatorMock);

    //     $action = new RegisterUserByPhoneIfNotExists($userRepositoryMock);

    //     try {
    //         $action->handle($data);
    //     } catch (\Exception $e) {
    //         Log::error('Exception in testHandleDoesNotRegisterUserIfExists', ['exception' => $e]);
    //         throw $e;
    //     }

    //     $this->assertTrue(true);

    //     Log::info('testHandleDoesNotRegisterUserIfExists completed');
    // }

    // /**
    //  * @runInSeparateProcess
    //  */
    // public function testHandleThrowsInvalidInputDataForInvalidPhone(): void
    // {
    //     Log::info('Starting testHandleThrowsInvalidInputDataForInvalidPhone');

    //     $this->expectException(InvalidInputData::class);
    //     $this->expectExceptionMessage('Invalid phone number');

    //     $data = new UserProfileData(
    //         phone: 'invalid_phone_number',
    //         name: 'Test User',
    //         email: 'test@example.com',
    //         password: 'password'
    //     );

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validatorMock = Mockery::mock('alias:Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($data->phone)
    //         ->andThrow(new InvalidInputData("Invalid phone number"));

    //     $this->app->instance(ValidateAuthenticationPhoneNumber::class, $validatorMock);

    //     $action = new RegisterUserByPhoneIfNotExists($userRepositoryMock);

    //     Log::info('About to handle action with invalid phone number');
    //     $action->handle($data);
    // }

    // protected function tearDown(): void
    // {
    //     Log::info('Tearing down test');
    //     Mockery::close();
    //     parent::tearDown();
    // }
}
