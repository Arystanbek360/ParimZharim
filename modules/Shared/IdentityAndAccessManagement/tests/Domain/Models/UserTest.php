<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Models;

use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{



    public function testUserCreationAndAttributes()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password' => 'password'
        ];

        $user = new User($userData);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('1234567890', $user->phone);

        $user->save();

        $fetchedUser = User::find($user->id);
        $this->assertNotNull($fetchedUser);
        $this->assertEquals('Test User', $fetchedUser->name);
    }


    public function testPasswordHashing()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }
}
