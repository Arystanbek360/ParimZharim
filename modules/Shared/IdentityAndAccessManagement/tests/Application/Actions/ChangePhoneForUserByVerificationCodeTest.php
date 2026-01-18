<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ChangePhoneForUserByVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneChangeRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Mockery;

class ChangePhoneForUserByVerificationCodeTest extends TestCase
{
    private $userRepository;
    private $changePhoneAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->changePhoneAction = new ChangePhoneForUserByVerificationCode($this->userRepository);

        // Мокирование статического метода ValidateAuthenticationPhoneNumber
        $validatorMock = Mockery::mock('overload:Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber');
        $validatorMock->shouldReceive('make')->andReturnSelf();
        $validatorMock->shouldReceive('handle')->andReturn(true);

        // Мокирование статического метода VerifyPhoneVerificationCodeForUser
        $verifierMock = Mockery::mock('overload:Modules\Shared\IdentityAndAccessManagement\Application\Actions\VerifyPhoneVerificationCodeForUser');
        $verifierMock->shouldReceive('make')->andReturnSelf();
        $verifierMock->shouldReceive('handle')->andReturnUsing(function ($user, $phone, $code) {
            error_log("Mock verifier handle called with phone: $phone and code: $code");
            if ($phone === '0987654321' && $code === '1234') {
                throw new AuthenticationError("Invalid code");
            }
            return true;
        });
    }

    public function testChangePhoneWithExistingPhoneNumberThrowsException()
    {
        $user = new User();
        $phoneData = new PhoneChangeRequestData($user, '1234567890', 1234);

        error_log('testChangePhoneWithExistingPhoneNumberThrowsException: Testing with phone number 1234567890.');

        $this->userRepository->shouldReceive('findByPhone')
            ->with('1234567890')
            ->andReturn(new User());

        $this->expectException(InvalidInputData::class);
        $this->expectExceptionMessage("Phone number already exists");

        $this->changePhoneAction->handle($phoneData);

        error_log('testChangePhoneWithExistingPhoneNumberThrowsException: Completed');
    }

    public function testChangePhoneWithIncorrectVerificationCodeThrowsException()
    {
        $user = new User();
        $phoneData = new PhoneChangeRequestData($user, '0987654321', 1234);

        error_log('testChangePhoneWithIncorrectVerificationCodeThrowsException: Testing with phone number 0987654321.');

        $this->userRepository->shouldReceive('findByPhone')
            ->with('0987654321')
            ->andReturn(null);

        $this->userRepository->shouldReceive('save')->never();

        $this->expectException(AuthenticationError::class);
        $this->expectExceptionMessage("Invalid code");

        $this->changePhoneAction->handle($phoneData);

        error_log('testChangePhoneWithIncorrectVerificationCodeThrowsException: Completed');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
