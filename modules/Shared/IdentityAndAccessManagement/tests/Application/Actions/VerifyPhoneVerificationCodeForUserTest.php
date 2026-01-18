<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\VerifyPhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class VerifyPhoneVerificationCodeForUserTest extends TestCase
{
    // protected $phoneVerificationCodeRepositoryMock;

    // public function setUp(): void
    // {
    //     parent::setUp();
    //     $this->phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
    // }

    // public function tearDown(): void
    // {
    //     Mockery::close();
    //     parent::tearDown();
    // }

    // public function testHandleSuccessfullyVerifiesCode(): void
    // {
    //     $user = new User();
    //     $phone = '+1234567890';
    //     $code = '123456';

    //     $phoneVerificationCode = new PhoneVerificationCode();
    //     $phoneVerificationCode->code = $code;

    //     $this->phoneVerificationCodeRepositoryMock
    //         ->shouldReceive('findLastAndActiveForUserAndPhone')
    //         ->once()
    //         ->with($user, $phone)
    //         ->andReturn($phoneVerificationCode);

    //     $this->phoneVerificationCodeRepositoryMock
    //         ->shouldReceive('markAsExpired')
    //         ->once()
    //         ->with($phoneVerificationCode);

    //     $action = new VerifyPhoneVerificationCodeForUser($this->phoneVerificationCodeRepositoryMock);

    //     $action->handle($user, $phone, $code);

    //     $this->expectNotToPerformAssertions(); // Test passes if no exceptions are thrown
    // }

    // public function testHandleThrowsExceptionForInvalidCode(): void
    // {
    //     $user = new User();
    //     $phone = '+1234567890';
    //     $code = '123456';

    //     $phoneVerificationCode = new PhoneVerificationCode();
    //     $phoneVerificationCode->code = '654321'; // Different code to simulate invalid code

    //     $this->phoneVerificationCodeRepositoryMock
    //         ->shouldReceive('findLastAndActiveForUserAndPhone')
    //         ->once()
    //         ->with($user, $phone)
    //         ->andReturn($phoneVerificationCode);

    //     $this->expectException(AuthenticationError::class);
    //     $this->expectExceptionMessage('Invalid code');

    //     $action = new VerifyPhoneVerificationCodeForUser($this->phoneVerificationCodeRepositoryMock);

    //     $action->handle($user, $phone, $code);
    // }

    // public function testHandleThrowsExceptionForNoCodeFound(): void
    // {
    //     $user = new User();
    //     $phone = '+1234567890';
    //     $code = '123456';

    //     $this->phoneVerificationCodeRepositoryMock
    //         ->shouldReceive('findLastAndActiveForUserAndPhone')
    //         ->once()
    //         ->with($user, $phone)
    //         ->andReturnNull();

    //     $this->expectException(AuthenticationError::class);
    //     $this->expectExceptionMessage('Invalid code');

    //     $action = new VerifyPhoneVerificationCodeForUser($this->phoneVerificationCodeRepositoryMock);

    //     $action->handle($user, $phone, $code);
    // }
    // protected function tearDown(): void
    // {
    //     Mockery::close();
    //     parent::tearDown();
    // }
}
