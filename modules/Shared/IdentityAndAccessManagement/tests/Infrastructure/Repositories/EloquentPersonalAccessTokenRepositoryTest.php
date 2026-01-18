<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories\EloquentPersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class EloquentPersonalAccessTokenRepositoryTest extends TestCase
{
    public function testDeleteByName()
    {
        $user = User::factory()->create();
        $tokenName = 'device_123';

        $user->createToken($tokenName);
        $repository = new EloquentPersonalAccessTokenRepository();
        $repository->deleteByName($user, $tokenName);

        $this->assertDatabaseMissing('idm_personal_access_tokens', ['name' => $tokenName]);
    }

    public function testDeleteCurrentUserToken()
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('test_token');
        $accessToken = $tokenResult->accessToken;

        Sanctum::actingAs($user);

        $user->withAccessToken($accessToken);

        $repository = new EloquentPersonalAccessTokenRepository();
        $repository->deleteCurrentUserToken($user);

        $this->assertDatabaseMissing('idm_personal_access_tokens', ['id' => $accessToken->id]);
    }

    public function testDeleteAllUserTokens()
    {
        $user = User::factory()->create();

        $user->createToken('device_1');
        $user->createToken('device_2');

        $repository = new EloquentPersonalAccessTokenRepository();
        $repository->deleteAllUserTokens($user);

        $this->assertDatabaseMissing('idm_personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);
    }
}
