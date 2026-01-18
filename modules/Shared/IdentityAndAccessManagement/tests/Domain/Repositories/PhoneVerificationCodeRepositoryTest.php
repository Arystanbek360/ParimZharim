<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Repositories;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;

class PhoneVerificationCodeRepositoryTest extends TestCase
{
    private $repository;
    private $user;
    private $phoneVerificationCode;

    protected function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->phoneVerificationCode = $this->createMock(PhoneVerificationCode::class);
        $this->repository = $this->createMock(PhoneVerificationCodeRepository::class);
    }

    public function testCreate()
    {
        $phone = '70000000000';
        $code = 666666;
        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->user, $phone, $code);

        $this->repository->create($this->user, $phone, $code);
    }

    public function testDeleteAllForUser()
    {
        $this->repository->expects($this->once())
            ->method('deleteAllForUser')
            ->with($this->user);

        $this->repository->deleteAllForUser($this->user);
    }

    public function testDeleteOlderThan()
    {
        $days = 60;
        $this->repository->expects($this->once())
            ->method('deleteOlderThan')
            ->with($days);

        $this->repository->deleteOlderThan($days);
    }

    public function testFindLastAndActiveForUserAndPhone()
    {
        $phone = '70000000000';
        $this->repository->expects($this->once())
            ->method('findLastAndActiveForUserAndPhone')
            ->with($this->user, $phone)
            ->willReturn($this->phoneVerificationCode);

        $this->assertEquals($this->phoneVerificationCode, $this->repository->findLastAndActiveForUserAndPhone($this->user, $phone));
    }

    public function testFindLastByPhone()
    {
        $phone = '0987654321';
        $this->repository->expects($this->once())
            ->method('findLastByPhone')
            ->with($phone)
            ->willReturn($this->phoneVerificationCode);

        $this->assertEquals($this->phoneVerificationCode, $this->repository->findLastByPhone($phone));
    }

    public function testCountAllByPhonePerDay()
    {
        $phone = '70000000000';
        $this->repository->expects($this->once())
            ->method('countAllByPhonePerDay')
            ->with($phone)
            ->willReturn(3);

        $this->assertEquals(3, $this->repository->countAllByPhonePerDay($phone));
    }

    public function testCountAllPerDay()
    {
        $this->repository->expects($this->once())
            ->method('countAllPerDay')
            ->willReturn(5);

        $this->assertEquals(5, $this->repository->countAllPerDay());
    }

    public function testMarkAsExpired()
    {
        $this->repository->expects($this->once())
            ->method('markAsExpired')
            ->with($this->phoneVerificationCode);

        $this->repository->markAsExpired($this->phoneVerificationCode);
    }
}
