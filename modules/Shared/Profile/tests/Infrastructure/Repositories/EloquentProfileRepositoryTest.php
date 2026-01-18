<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Natifood\Profile\Domain\Models\ProfileType;
use Modules\Shared\Core\Tests\BaseTestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Infrastructure\Repositories\EloquentProfileRepository;

/**
 * Тесты для репозитория EloquentProfileRepository.
 *
 * Проверяет общие методы работы с профилями.
 */
class EloquentProfileRepositoryTest extends BaseTestCase
{
    use RefreshDatabase;

    private EloquentProfileRepository $repository;
    private User $user;
    private Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->repository = new EloquentProfileRepository();

        // Создание тестового пользователя и профиля
        $this->user = User::factory()->create();
        $this->profile = Profile::create(
            [
                'name' => 'Test User',
                'email' => 'test@test.com',
                'phone' => '1234567890',
                'type' => ProfileType::CUSTOMER,
                'user_id' => $this->user->id
            ]
        );
    }

    public function testCreateAssociatedUserForProfileIfNotExists(): void
    {
        $user = User::factory()->make();
        $profile = new Profile();
        $profile->name = 'Test User';
        $profile->email = 'unique_' . Str::random(10) . '@test.com';
        $profile->phone = '+71234567890';
        $profile->type = ProfileType::CUSTOMER;
        $profile->save();
        $this->repository->createAssociatedUserForProfileIfNotExists($profile);

        $profile = $profile->refresh();
        $this->assertNotNull($profile->refresh()->user_id);
        $this->assertDatabaseHas('idm_users', ['id' => $profile->user_id]);
    }

    public function testUpdateAssociatedUserForProfile(): void
    {
        $this->profile->email = 'updated@example.com';
        $this->repository->updateAssociatedUserForProfile($this->profile);

        $this->assertEquals('updated@example.com', $this->profile->user->refresh()->email);
    }

    public function testGetProfileById(): void
    {
        $retrievedProfile = $this->repository->getProfileById($this->profile->id);

        $this->assertInstanceOf(Profile::class, $retrievedProfile);
        $this->assertEquals($this->profile->id, $retrievedProfile->id);
    }

    public function testDeleteProfileWithUser(): void
    {
        $this->repository->deleteProfileWithUser($this->profile);

        $this->assertSoftDeleted('idm_users', ['id' => $this->user->id]);
    }
}
