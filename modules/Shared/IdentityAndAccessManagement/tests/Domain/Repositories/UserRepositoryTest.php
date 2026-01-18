<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Repositories;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class UserRepositoryTest extends TestCase
{
    private $repository;
    private $user;

    protected function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->repository = $this->createMock(UserRepository::class);
    }

    public function testFindByEmail()
    {
        $email = 'example@example.com';
        $this->repository->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($this->user);

        $this->assertSame($this->user, $this->repository->findByEmail($email));
    }

    public function testFindByPhone()
    {
        $phone = '70000000000';
        $this->repository->expects($this->once())
            ->method('findByPhone')
            ->with($phone)
            ->willReturn($this->user);

        $this->assertSame($this->user, $this->repository->findByPhone($phone));
    }

    public function testFindById()
    {
        $id = '123';
        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($this->user);

        $this->assertSame($this->user, $this->repository->findById($id));
    }

    public function testSave()
    {
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->user);

        $this->repository->save($this->user);
    }
}
