<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Domain\Policies;

use Modules\ParimZharim\Objects\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Domain\Policies\TagPolicy;

class TagPolicyTest extends TestCase
{
    private TagPolicy $tagPolicy;
    private User $user;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagPolicy = new TagPolicy();
        $this->user = $this->createMock(User::class);
        $this->tag = $this->createMock(Tag::class);
    }

    public function testViewAnyWithPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(true);
        $this->assertTrue($this->tagPolicy->viewAny($this->user));
    }

    public function testViewAnyWithoutPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(false);
        $this->assertNull($this->tagPolicy->viewAny($this->user));
    }

    public function testCreateWithPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(true);
        $this->assertTrue($this->tagPolicy->create($this->user));
    }

    public function testCreateWithoutPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(false);
        $this->assertNull($this->tagPolicy->create($this->user));
    }

    public function testUpdateWithPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(true);
        $this->assertTrue($this->tagPolicy->update($this->user, $this->tag));
    }

    public function testUpdateWithoutPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(false);
        $this->assertNull($this->tagPolicy->update($this->user, $this->tag));
    }

    public function testDeleteWithPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(true);
        $this->assertTrue($this->tagPolicy->delete($this->user, $this->tag));
    }

    public function testDeleteWithoutPermission()
    {
        $this->user->method('hasPermissionTo')->willReturn(false);
        $this->assertNull($this->tagPolicy->delete($this->user, $this->tag));
    }

    public function testForceDeleteAlwaysReturnsFalse()
    {
        $this->assertFalse($this->tagPolicy->forceDelete($this->user, $this->tag));
    }
}
