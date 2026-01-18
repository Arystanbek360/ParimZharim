<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Policies;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Policies\ProductPolicy;
use Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions\ProductsAndServicesPermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    protected $user;
    protected $product;
    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createMock(User::class);
        $this->product = $this->createMock(Product::class);
        $this->policy = new ProductPolicy();
    }

    /** @test */
    public function it_allows_view_any_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->viewAny($this->user));
    }

    /** @test */
    public function it_allows_view_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->view($this->user, $this->product));
    }

    /** @test */
    public function it_allows_create_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->create($this->user));
    }

    /** @test */
    public function it_allows_update_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->update($this->user, $this->product));
    }

    /** @test */
    public function it_allows_replicate_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->replicate($this->user, $this->product));
    }

    /** @test */
    public function it_denies_force_delete()
    {
        $this->assertFalse($this->policy->forceDelete($this->user, $this->product));
    }

    /** @test */
    public function it_allows_delete_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->delete($this->user, $this->product));
    }

    /** @test */
    public function it_allows_restore_if_user_has_permission()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(true);

        $this->assertTrue($this->policy->restore($this->user, $this->product));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_view_any()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->viewAny($this->user));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_view()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->view($this->user, $this->product));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_create()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->create($this->user));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_update()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->update($this->user, $this->product));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_replicate()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->replicate($this->user, $this->product));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_delete()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->delete($this->user, $this->product));
    }

    /** @test */
    public function it_returns_null_if_user_does_not_have_permission_for_restore()
    {
        $this->user->method('hasPermissionTo')->with(ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES)->willReturn(false);

        $this->assertNull($this->policy->restore($this->user, $this->product));
    }
}
