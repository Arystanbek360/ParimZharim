<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Policies\UserPolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\UserPermission;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        // Create necessary permissions
        Permission::create(['name' => UserPermission::VIEW_USERS->value]);
        Permission::create(['name' => UserPermission::MANAGE_USERS->value]);
        Permission::create(['name' => UserPermission::MANAGE_USER_ROLES->value]);
    }

    public function testViewAnyPermission()
    {
        $user = $this->getUserWithPermission(UserPermission::VIEW_USERS);
        $policy = new UserPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    public function testViewPermission()
    {
        $user = $this->getUserWithPermission(UserPermission::VIEW_USERS);
        $model = User::factory()->create();  // Assuming you have a User factory set up.
        $policy = new UserPolicy();

        $this->assertTrue($policy->view($user, $model));
    }

    public function testCreatePermission()
    {
        $user = $this->getUserWithPermission(UserPermission::MANAGE_USERS);
        $policy = new UserPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function testUpdatePermission()
    {
        $user = $this->getUserWithPermission(UserPermission::MANAGE_USERS);
        $model = User::factory()->create();  // Assuming you have a User factory set up.
        $policy = new UserPolicy();

        $this->assertTrue($policy->update($user, $model));
    }

    public function testReplicatePermission()
    {
        $user = User::factory()->create();
        $model = User::factory()->create();  // Assuming you have a User factory set up.
        $policy = new UserPolicy();

        $this->assertFalse($policy->replicate($user, $model));
    }

    public function testDeletePermission()
    {
        $user = User::factory()->create();
        $model = User::factory()->create();  // Assuming you have a User factory set up.
        $policy = new UserPolicy();

        $this->assertFalse($policy->delete($user, $model));
    }

    public function testAttachRolePermission()
    {
        $user = $this->getUserWithPermission(UserPermission::MANAGE_USER_ROLES);
        $model = User::factory()->create();  // Assuming you have a User factory set up.
        $policy = new UserPolicy();

        $this->assertTrue($policy->attachRole($user, $model));
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
