<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PersonalAccessToken;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class PersonalAccessTokenTest extends TestCase
{


    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function testPersonalAccessTokenCreationAndPersistence()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $token = new PersonalAccessToken();
        $token->name = 'test-token';
        $token->token = 'random-token-string';
        $token->abilities = json_encode(['*']);
        $token->tokenable_id = $user->id;
        $token->tokenable_type = get_class($user);

        $token->save();

        $fetchedToken = PersonalAccessToken::find($token->id);
        $this->assertNotNull($fetchedToken);
        $this->assertEquals('test-token', $fetchedToken->name);
        $this->assertEquals($user->id, $fetchedToken->tokenable_id);
        $this->assertEquals(get_class($user), $fetchedToken->tokenable_type);
    }
}
