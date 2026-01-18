<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByEmail;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\EmailAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;  
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Illuminate\Support\Facades\Auth;
use Mockery;

class AuthenticateUserByEmailTest extends TestCase
{
    private $userRepository;
    private $authenticateUserByEmail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->authenticateUserByEmail = new AuthenticateUserByEmail($this->userRepository);

        Auth::shouldReceive('attempt')
            ->andReturnUsing(function ($credentials) {
                return $credentials['password'] === 'correctpassword';
            });

        Auth::shouldReceive('login')->andReturnUsing(function ($user) {
            return 'some-token'; // Просто возвращаем строку для примера
        });
    }

    public function testInvalidEmailThrowsException()
    {
        $this->expectException(InvalidInputData::class);
        $requestData = new EmailAuthenticationRequestData('invalid-email', 'password123');
        $this->authenticateUserByEmail->handle($requestData);
    }

    public function testUserNotFoundThrowsException()
    {
        $this->expectException(UserNotFound::class);
        $requestData = new EmailAuthenticationRequestData('user@example.com', 'password123');
        $this->userRepository->shouldReceive('findByEmail')
            ->with('user@example.com')
            ->andReturn(null);
        $this->authenticateUserByEmail->handle($requestData);
    }

    public function testInvalidPasswordThrowsException()
    {
        $this->expectException(AuthenticationError::class);
        $requestData = new EmailAuthenticationRequestData('user@example.com', 'wrongpassword');
        $user = Mockery::mock(User::class); // Создаем мок модели User
        $this->userRepository->shouldReceive('findByEmail')
            ->with('user@example.com')
            ->andReturn($user);
        $this->authenticateUserByEmail->handle($requestData);
    }

    public function testSuccessfulAuthenticationReturnsToken()
    {
        $requestData = new EmailAuthenticationRequestData('user@example.com', 'correctpassword');
        $user = Mockery::mock(User::class); // Создаем мок модели User
        $user->shouldReceive('createToken')
            ->andReturnUsing(function ($string) {
                return new class {
                    public $plainTextToken = 'some-token';
                };
            });
        $this->userRepository->shouldReceive('findByEmail')
            ->with('user@example.com')
            ->andReturn($user);
        $token = $this->authenticateUserByEmail->handle($requestData);
        $this->assertEquals('some-token', $token);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
