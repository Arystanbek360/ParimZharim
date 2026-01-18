<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Adapters\Api\ApiControllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ChangePhoneForUserByVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RequestPhoneChangePhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\UpdateUserProfile;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;


class ProfileApiControllerTest extends TestCase
{


    public function testRequestPhoneChangePhoneCode()
    {
        $this->mock(RequestPhoneChangePhoneVerificationCodeForUser::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Create a user
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        // Prepare the request data
        $requestData = [
            'phone' => '+1234567890',
        ];

        // Send the request and get the response
        $response = $this->postJson('/api/idm/request-phone-change-phone-code', $requestData);

        // Assert the response status
        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);
    }

    public function testChangePhone()
    {
        $this->mock(ChangePhoneForUserByVerificationCode::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Create a user
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        // Prepare the request data
        $requestData = [
            'phone' => '+1234567890',
            'code' => '123456',
        ];

        // Send the request and get the response
        $response = $this->postJson('/api/idm/change-phone', $requestData);

        // Assert the response status
        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);

    }

    public function testGetProfile()
    {
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        $response = $this->getJson('/api/idm/get-profile');

        $response->assertStatus(JsonResponse::HTTP_OK);

        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            // другие поля, если необходимо
        ]);

    }

    public function testUpdateProfile()
    {
        $this->mock(UpdateUserProfile::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(true);
        });

        // Create a user
        $user = User::factory()->create();

        // Acting as the created user
        $this->actingAs($user);

        // Prepare the request data
        $requestData = [
            'name' => 'Updated Name',
        ];

        $response = $this->postJson('/api/idm/update-profile', $requestData);

        $response->assertStatus(JsonResponse::HTTP_OK);

        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            // другие поля, если необходимо
        ]);

    }
}
