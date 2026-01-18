<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Infrastructure\Repositories;

use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentPhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class EloquentPhoneVerificationCodeRepositoryTest extends TestCase
{
    private EloquentPhoneVerificationCodeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentPhoneVerificationCodeRepository();
    }

    public function testCreateCreatesVerificationCode(): void
    {
        // Arrange
        $user = User::factory()->create();
        $phone = '70000000000';
        $code = 666666;

        // Act
        $this->repository->create($user, $phone, $code);

        // Assert
        $this->assertDatabaseHas('idm_phone_verification_codes', [
            'user_id' => $user->id,
            'phone' => $phone,
            'code' => $code
        ]);
    }
}
