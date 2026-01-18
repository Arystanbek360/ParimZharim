<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Policies\PermissionPolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\PermissionPermission;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Tests\TestCase;

class PermissionPolicyTest extends TestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        $this->permission = Permission::create(['name' => PermissionPermission::VIEW_PERMISSIONS->value]);
    }

    public function testViewAnyPermission()
    {
        $user = $this->getUserWithPermission(PermissionPermission::VIEW_PERMISSIONS);
        $policy = new PermissionPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    public function testViewPermission()
    {
        $user = $this->getUserWithPermission(PermissionPermission::VIEW_PERMISSIONS);
        $permission = new Permission($this->permission->toArray());
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->view($user, $permission));
    }

    public function testCreatePermission()
    {
        $user = User::factory()->create();
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->create($user));
    }

    public function testUpdatePermission()
    {
        $user = User::factory()->create();
        $permission = new Permission($this->permission->toArray());
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->update($user, $permission));
    }

    public function testDeletePermission()
    {
        $user = User::factory()->create();
        $permission = new Permission($this->permission->toArray());
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->delete($user, $permission));
    }

    public function testReplicatePermission()
    {
        $user = User::factory()->create();
        $permission = new Permission($this->permission->toArray());
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->replicate($user, $permission));
    }

    public function testForceDeletePermission()
    {
        $user = User::factory()->create();
        $permission = new Permission($this->permission->toArray());
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->forceDelete($user, $permission));
    }

    public function testRestorePermission()
    {
        $user = User::factory()->create();
        $permission = new Permission($this->permission->toArray());
        $policy = new PermissionPolicy();

        $this->assertFalse($policy->restore($user, $permission));
    }

    // Helper method to create a user with given permissions
    protected function getUserWithPermission(...$permissions)
    {
        $user = User::factory()->create();  // Assuming you have a User factory set up.
        foreach ($permissions as $permission) {
            $user->givePermissionTo($permission);
        }
        return $user;
    }
}
