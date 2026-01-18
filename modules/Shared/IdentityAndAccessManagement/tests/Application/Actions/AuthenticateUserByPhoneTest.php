<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByPhone;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User; 
use Illuminate\Support\Facades\Auth;
use Mockery;

class AuthenticateUserByPhoneTest extends TestCase
{
    private $userRepository;
    private $authenticateUserByPhone;
    private $validatePhoneMock;
    private $verifyCodeMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->authenticateUserByPhone = new AuthenticateUserByPhone($this->userRepository);
    
        // Мокирование метода save, который будет возвращать true, предполагая, что сохранение прошло успешно
        $this->userRepository->shouldReceive('save')
            ->andReturn(true);
    
        $this->validatePhoneMock = Mockery::mock('alias:Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber');
        $this->validatePhoneMock->shouldReceive('make')->andReturnSelf();
    
        $this->verifyCodeMock = Mockery::mock('alias:Modules\Shared\IdentityAndAccessManagement\Application\Actions\VerifyPhoneVerificationCodeForUser');
        $this->verifyCodeMock->shouldReceive('make')->andReturnSelf();
    
        Auth::shouldReceive('login')->andReturnUsing(function ($user) {
            return 'logged-in'; // Просто возвращаем строку для примера
        });
    }
    

    public function testInvalidPhoneNumberThrowsException()
    {
        $this->validatePhoneMock->shouldReceive('handle')->andThrow(new InvalidInputData("Invalid phone number"));
        $requestData = new PhoneAuthenticationRequestData('invalid-phone', 1234, 'WEB');

        $this->expectException(InvalidInputData::class);
        $this->authenticateUserByPhone->handle($requestData);
    }

    public function testUserNotFoundThrowsException()
    {
        $this->validatePhoneMock->shouldReceive('handle')->with('valid-phone');
        $requestData = new PhoneAuthenticationRequestData('valid-phone', 1234, 'WEB');
        $this->userRepository->shouldReceive('findByPhone')
            ->with('valid-phone')
            ->andReturn(null);

        $this->expectException(UserNotFound::class);
        $this->authenticateUserByPhone->handle($requestData);
    }

    public function testInvalidCodeThrowsException()
    {
        $this->validatePhoneMock->shouldReceive('handle')->with('valid-phone');
        $this->verifyCodeMock->shouldReceive('handle')->andThrow(new AuthenticationError("Invalid code"));
        $requestData = new PhoneAuthenticationRequestData('valid-phone', 9999, 'WEB');
        $user = $this->setUpUserMock();
        $this->userRepository->shouldReceive('findByPhone')
            ->with('valid-phone')
            ->andReturn($user);

        $this->expectException(AuthenticationError::class);
        $this->authenticateUserByPhone->handle($requestData);
    }

    public function testSuccessfulAuthenticationReturnsToken()
    {
        $this->validatePhoneMock->shouldReceive('handle')->with('valid-phone');
        $this->verifyCodeMock->shouldReceive('handle')->with(Mockery::type(User::class), 'valid-phone', 1234);
        $requestData = new PhoneAuthenticationRequestData('valid-phone', 1234, 'MOBILE');
        $user = $this->setUpUserMock();
        $this->userRepository->shouldReceive('findByPhone')
            ->with('valid-phone')
            ->andReturn($user);

        $token = $this->authenticateUserByPhone->handle($requestData);
        $this->assertEquals('some-token', $token);
    }

    private function setUpUserMock()
{
    $user = Mockery::mock(User::class);

    // Мокируем получение атрибутов
    $user->shouldReceive('getAttribute')
         ->andReturnUsing(function ($key) {
             $attributes = [
                 'id' => 1,
                 'phone_verified_at' => null,
                 'email' => 'user@example.com',
                 'phone' => '1234567890',
                 // Добавьте другие атрибуты по мере необходимости
             ];

             return $attributes[$key] ?? null;
         });

    // Мокируем установку атрибутов
    $user->shouldReceive('setAttribute')
         ->andReturnUsing(function ($key, $value) {
            // Мокируем поведение метода setAttribute, чтобы эмулировать установку значений для атрибутов.
            // Это может быть полезно, если в вашем коде есть логика, зависящая от изменения состояния модели.
            if ($key === 'phone_verified_at' && !$value) {
                throw new \InvalidArgumentException("Value for 'phone_verified_at' cannot be null");
            }
            // Здесь вы можете добавить условия и логику для разных ключей атрибутов.
        });

    // Мокируем создание токена
    $user->shouldReceive('createToken')->andReturnUsing(function ($deviceId) {
        // Мокируем создание токена, возвращаем объект с необходимым свойством.
        return new class {
            public $plainTextToken = 'some-token';
        };
    });

    // В случае, если ваш код требует сохранения изменений пользователя, следует мокировать и метод save.
    $user->shouldReceive('save')->andReturn(true);

    return $user;
}

    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
