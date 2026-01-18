<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Adapters\Api\ApiControllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByPhone;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetUserProfileByPhone;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RegisterUserByPhoneIfNotExists;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RequestAuthenticationPhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutDevice;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByEmail;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutCurrentDevice;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutAllDevices;


class AuthApiControllerTest extends TestCase
{


    public function testRequestAuthPhoneCode()
    {
        $this->mock(RegisterUserByPhoneIfNotExists::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        $user = User::factory()->create(['phone' => '+1234567890']);
        $this->mock(GetUserProfileByPhone::class, function ($mock) use ($user) {
            $mock->shouldReceive('handle')->andReturn($user);
        });

        $this->mock(RequestAuthenticationPhoneVerificationCodeForUser::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Prepare the request data
        $requestData = [
            'phone' => '+1234567890',
        ];

        $response = $this->postJson('/api/idm/request-auth-phone-code', $requestData);
        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);

    }

    public function testGetAccessTokenByAuthPhoneCode()
    {
        $user = User::factory()->create(['phone' => '+1234567890']);
        $this->mock(GetUserProfileByPhone::class, function ($mock) use ($user) {
            $mock->shouldReceive('handle')->andReturn($user);
        });

        $this->mock(AuthenticateUserByPhone::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn('test-token');
        });

        // Prepare the request data
        $requestData = [
            'phone' => '+1234567890',
            'code' => '123456',
            'device_id' => 'test-device-id',
        ];

        $response = $this->postJson('/api/idm/get-access-token-by-auth-phone-code', $requestData);

        $response->assertStatus(JsonResponse::HTTP_OK);

        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'phone',
            ],
        ]);

    }

    public function testLogoutDevice()
    {

        $this->mock(LogoutDevice::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Create a user
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        // Prepare the request data
        $requestData = [
            'device_id' => 'test-device-id',
        ];

        $response = $this->postJson('/api/idm/logout-device', $requestData);
        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);
    }

    public function testLogoutCurrentDevice()
    {

        $this->mock(LogoutCurrentDevice::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Create a user
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        $response = $this->postJson('/api/idm/logout-current-device');
        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);

    }

    public function testLogoutAllDevices()
    {
        $this->mock(LogoutAllDevices::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Create a user
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        $response = $this->postJson('/api/idm/logout-all-devices');
        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);
    }

    public function testGetAccessTokenByEmailPassword()
    {
        $this->mock(AuthenticateUserByEmail::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn('test-token');
        });

        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        // Acting as the created user
        $this->actingAs($user);

        // Prepare the request data
        $requestData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/idm/get-access-token-by-auth-email-password', $requestData);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email',

            ],
        ]);

    }
}
