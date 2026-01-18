<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentUserRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository();
    }

    public function testFindByEmailReturnsUser(): void
    {
        // Arrange
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);

        // Act
        $foundUser = $this->repository->findByEmail($email);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testFindByPhoneReturnsUser(): void
    {
        // Arrange
        $phone = '70000000000';
        $user = User::factory()->create(['phone' => $phone]);

        // Act
        $foundUser = $this->repository->findByPhone($phone);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testFindByIdReturnsUser(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $foundUser = $this->repository->findById((string)$user->id);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testSaveStoresUser(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Act
        $this->repository->save($user);

        // Assert
        $this->assertDatabaseHas('idm_users', ['email' => 'test@example.com']);
    }
}
