<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Repositories;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class PersonalAccessTokenRepositoryTest extends TestCase
{
    private $repository;
    private $user;

    protected function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->repository = $this->createMock(PersonalAccessTokenRepository::class);
    }

    public function testDeleteByName()
    {
        $deviceId = 'device123';
        $this->repository->expects($this->once())
            ->method('deleteByName')
            ->with($this->user, $deviceId);

        $this->repository->deleteByName($this->user, $deviceId);
    }

    public function testDeleteCurrentUserToken()
    {
        $this->repository->expects($this->once())
            ->method('deleteCurrentUserToken')
            ->with($this->user);

        $this->repository->deleteCurrentUserToken($this->user);
    }

    public function testDeleteAllUserTokens()
    {
        $this->repository->expects($this->once())
            ->method('deleteAllUserTokens')
            ->with($this->user);

        $this->repository->deleteAllUserTokens($this->user);
    }
}
