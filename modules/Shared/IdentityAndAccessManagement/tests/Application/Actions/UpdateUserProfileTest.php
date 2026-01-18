<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\UpdateUserProfile;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserProfileTest extends TestCase
{
    use MockeryPHPUnitIntegration;


    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHandleUpdatesUserProfile(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $data = new UserProfileData(
            phone: $user->phone,
            name: 'New Name',
            email: $user->email,
            password: $user->password
        );

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($user) use ($data) {
                return $user->name === $data->name;
            }));

        $this->app->instance(UserRepository::class, $userRepositoryMock);

        $action = new UpdateUserProfile($userRepositoryMock);
        $action->handle($user, $data);

        $this->assertEquals('New Name', $user->name);
    }

    public function testHandleDoesNotUpdateUserNameIfNull(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $data = new UserProfileData(
            phone: $user->phone,
            name: null,
            email: $user->email,
            password: $user->password
        );

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($user) {
                return $user->name === 'Old Name';
            }));

        $this->app->instance(UserRepository::class, $userRepositoryMock);

        $action = new UpdateUserProfile($userRepositoryMock);
        $action->handle($user, $data);

        $this->assertEquals('Old Name', $user->name);
    }
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
