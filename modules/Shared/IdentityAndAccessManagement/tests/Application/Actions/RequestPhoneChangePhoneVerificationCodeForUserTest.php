<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RequestPhoneChangePhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Tests\Stubs\ValidateAuthenticationPhoneNumberStub;
use Modules\Shared\IdentityAndAccessManagement\Tests\Stubs\SendPhoneVerificationCodeForUserStub;
use Mockery;
use Random\RandomException;

class RequestPhoneChangePhoneVerificationCodeForUserTest extends TestCase
{
    // protected function setUp(): void
    // {
    //     parent::setUp();
    // }

    // public function testHandleSendsVerificationCodeIfPhoneIsValid(): void
    // {
    //     $user = Mockery::mock(User::class);
    //     $phone = '+1234567890';

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $userRepositoryMock->shouldReceive('findByPhone')
    //         ->with($phone)
    //         ->andReturnNull();

    //     $phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
    //     $smsServiceMock = Mockery::mock(SmsService::class);

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validator = new ValidateAuthenticationPhoneNumberStub();
    //     $sender = new SendPhoneVerificationCodeForUserStub();

    //     $action = new RequestPhoneChangePhoneVerificationCodeForUser(
    //         $userRepositoryMock,
    //         $phoneVerificationCodeRepositoryMock,
    //         $smsServiceMock,
    //         $validator,
    //         $sender
    //     );

    //     $action->handle($user, $phone);

    //     $this->assertTrue(true);
    // }

    // public function testHandleThrowsInvalidInputDataForInvalidPhone(): void
    // {
    //     $this->expectException(InvalidInputData::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = 'invalid_phone_number';

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
    //     $smsServiceMock = Mockery::mock(SmsService::class);

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validator = new ValidateAuthenticationPhoneNumberStub();
    //     $sender = new SendPhoneVerificationCodeForUserStub();

    //     $action = new RequestPhoneChangePhoneVerificationCodeForUser(
    //         $userRepositoryMock,
    //         $phoneVerificationCodeRepositoryMock,
    //         $smsServiceMock,
    //         $validator,
    //         $sender
    //     );

    //     $action->handle($user, $phone);
    // }

    // public function testHandleThrowsPhoneVerificationCodeRateLimitError(): void
    // {
    //     $this->expectException(PhoneVerificationCodeRateLimitError::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = 'rate_limited_phone';

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $userRepositoryMock->shouldReceive('findByPhone')
    //         ->with($phone)
    //         ->andReturnNull();

    //     $phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
    //     $smsServiceMock = Mockery::mock(SmsService::class);

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validator = new ValidateAuthenticationPhoneNumberStub();
    //     $sender = new SendPhoneVerificationCodeForUserStub();

    //     $action = new RequestPhoneChangePhoneVerificationCodeForUser(
    //         $userRepositoryMock,
    //         $phoneVerificationCodeRepositoryMock,
    //         $smsServiceMock,
    //         $validator,
    //         $sender
    //     );

    //     $action->handle($user, $phone);
    // }

    // public function testHandleThrowsRandomException(): void
    // {
    //     $this->expectException(RandomException::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = 'random_error_phone';

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $userRepositoryMock->shouldReceive('findByPhone')
    //         ->with($phone)
    //         ->andReturnNull();

    //     $phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
    //     $smsServiceMock = Mockery::mock(SmsService::class);

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validator = new ValidateAuthenticationPhoneNumberStub();
    //     $sender = new SendPhoneVerificationCodeForUserStub();

    //     $action = new RequestPhoneChangePhoneVerificationCodeForUser(
    //         $userRepositoryMock,
    //         $phoneVerificationCodeRepositoryMock,
    //         $smsServiceMock,
    //         $validator,
    //         $sender
    //     );

    //     $action->handle($user, $phone);
    // }

    // public function testHandleThrowsInvalidInputDataIfPhoneIsAlreadyInUse(): void
    // {
    //     $this->expectException(InvalidInputData::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = '+1234567890';

    //     $existingUser = Mockery::mock(User::class);

    //     $userRepositoryMock = Mockery::mock(UserRepository::class);
    //     $userRepositoryMock->shouldReceive('findByPhone')
    //         ->with($phone)
    //         ->andReturn($existingUser);

    //     $phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
    //     $smsServiceMock = Mockery::mock(SmsService::class);

    //     $this->app->instance(UserRepository::class, $userRepositoryMock);

    //     $validator = new ValidateAuthenticationPhoneNumberStub();
    //     $sender = new SendPhoneVerificationCodeForUserStub();

    //     $action = new RequestPhoneChangePhoneVerificationCodeForUser(
    //         $userRepositoryMock,
    //         $phoneVerificationCodeRepositoryMock,
    //         $smsServiceMock,
    //         $validator,
    //         $sender
    //     );

    //     $action->handle($user, $phone);
    // }
    // protected function tearDown(): void
    // {
    //     Mockery::close();
    //     parent::tearDown();
    // }
}
