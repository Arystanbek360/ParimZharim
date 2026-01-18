<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Tests\Domain\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Policies\ProfilePolicy;
use Modules\Shared\Profile\Tests\TestCase;

class ProfilePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function testViewAnyPermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();

        $this->assertNull($policy->viewAny($user));
    }

    public function testViewPermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();

        $this->assertNull($policy->view($user, $profile));
    }

    public function testCreatePermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();

        $this->assertNull($policy->create($user));
    }

    public function testUpdatePermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();

        $this->assertNull($policy->update($user, $profile));
    }

    public function testReplicatePermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();

        $this->assertFalse($policy->replicate($user, $profile));
    }

    public function testForceDeletePermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();

        $this->assertFalse($policy->forceDelete($user, $profile));
    }

    public function testDeletePermissionAdminEmail()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();
        $profile->email = 'admin@arista.kz';

        $this->assertFalse($policy->delete($user, $profile));
    }

    public function testDeletePermissionOwnProfile()
    {
        $user = User::factory()->create();
        $profile = new Profile();
        $profile->user_id = $user->id;
        $policy = new ProfilePolicy();

        $this->assertFalse($policy->delete($user, $profile));
    }

    public function testDeletePermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();

        $this->assertNull($policy->delete($user, $profile));
    }

    public function testRestorePermission()
    {
        $user = User::factory()->create();
        $policy = new ProfilePolicy();
        $profile = new Profile();

        $this->assertNull($policy->restore($user, $profile));
    }
}
