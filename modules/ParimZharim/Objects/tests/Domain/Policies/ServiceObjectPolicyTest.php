<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Domain\Policies;

use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Domain\Policies\ServiceObjectPolicy;
use Modules\ParimZharim\Objects\Domain\RolesAndPermissions\ObjectPermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Spatie\Permission\Models\Permission;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceObjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceObjectPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ServiceObjectPolicy();

        // Создание разрешений для тестирования
        Permission::create(['name' => ObjectPermission::VIEW_OBJECTS, 'guard_name' => 'web']);
        Permission::create(['name' => ObjectPermission::MANAGE_OBJECTS, 'guard_name' => 'web']);
    }

    public function test_view_any_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::VIEW_OBJECTS);

        $userWithoutPermission = User::factory()->create();

        $this->assertTrue($this->policy->viewAny($userWithPermission));
        $this->assertNull($this->policy->viewAny($userWithoutPermission));
    }

    public function test_view_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::VIEW_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->view($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->view($userWithoutPermission, $serviceObject));
    }

    public function test_create_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();

        $this->assertTrue($this->policy->create($userWithPermission));
        $this->assertNull($this->policy->create($userWithoutPermission));
    }

    public function test_update_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->update($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->update($userWithoutPermission, $serviceObject));
    }

    public function test_replicate_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->replicate($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->replicate($userWithoutPermission, $serviceObject));
    }

    public function test_force_delete_permission()
    {
        $user = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertFalse($this->policy->forceDelete($user, $serviceObject));
    }

    public function test_delete_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->delete($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->delete($userWithoutPermission, $serviceObject));
    }

    public function test_attach_any_tags_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->attachAnyTags($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->attachAnyTags($userWithoutPermission, $serviceObject));
    }

    public function test_attach_tags_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->attachTags($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->attachTags($userWithoutPermission, $serviceObject));
    }

    public function test_detach_tags_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->detachTags($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->detachTags($userWithoutPermission, $serviceObject));
    }

    public function test_restore_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $serviceObject = ServiceObject::factory()->create();

        $this->assertTrue($this->policy->restore($userWithPermission, $serviceObject));
        $this->assertNull($this->policy->restore($userWithoutPermission, $serviceObject));
    }
}
