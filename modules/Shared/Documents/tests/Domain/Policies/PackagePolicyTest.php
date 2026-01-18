<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Policies\PackagePolicy;
use Modules\Shared\Documents\Domain\Services\PackageService;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;


class PackagePolicyTest extends TestCase
{


    protected Package $publicPackageToRead;
    protected Package $publicPackageToWrite;
    protected Package $protectedPackage;

    protected User $creator;
    protected User $specificUserCanRead;
    protected User $specificUserCanWrite;
    protected User $anyUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPackageToRead = Package::factory()->create();
        $this->publicPackageToWrite = Package::factory()->create();
        $this->protectedPackage = Package::factory()->create();

        $this->creator = User::factory()->create();
        $this->specificUserCanRead = User::factory()->create();
        $this->specificUserCanWrite = User::factory()->create();
        $this->anyUser = User::factory()->create();

        $this->publicPackageToRead->access_mode = AccessMode::ANY_USER;
        $this->publicPackageToWrite->access_mode = AccessMode::ANY_USER;
        $this->protectedPackage->access_mode = AccessMode::SPECIFIC_USERS;
        $this->publicPackageToRead->default_access_type = AccessType::READ;
        $this->publicPackageToWrite->default_access_type = AccessType::WRITE;

        $this->publicPackageToRead->creator_id = $this->creator->id;
        $this->publicPackageToWrite->creator_id = $this->creator->id;
        $this->protectedPackage->creator_id = $this->creator->id;

        $this->protectedPackage->users()->attach($this->specificUserCanRead->id);
        $this->protectedPackage->users()->updateExistingPivot($this->specificUserCanRead->id, ['access_type' => AccessType::READ]);
        $this->protectedPackage->users()->attach($this->specificUserCanWrite->id);
        $this->protectedPackage->users()->updateExistingPivot($this->specificUserCanWrite->id, ['access_type' => AccessType::WRITE]);
    }

    public function testViewAny(): void
    {
        $policy = new PackagePolicy();

        $this->assertTrue($policy->viewAny($this->creator));
        $this->assertTrue($policy->viewAny($this->specificUserCanRead));
        $this->assertTrue($policy->viewAny($this->specificUserCanWrite));
        $this->assertTrue($policy->viewAny($this->anyUser));
    }

    public function testView(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->view($this->creator, $package));
        $this->assertTrue($policy->view($this->specificUserCanRead, $package));
        $this->assertTrue($policy->view($this->specificUserCanWrite, $package));
        $this->assertTrue($policy->view($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->view($this->creator, $package));
        $this->assertTrue($policy->view($this->specificUserCanRead, $package));
        $this->assertTrue($policy->view($this->specificUserCanWrite, $package));
        $this->assertTrue($policy->view($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->view($this->creator, $package));
        $this->assertTrue($policy->view($this->specificUserCanRead, $package));
        $this->assertTrue($policy->view($this->specificUserCanWrite, $package));
        $this->assertNull($policy->view($this->anyUser, $package));
    }

    public function testCreate(): void
    {
        $policy = new PackagePolicy();

        $this->assertTrue($policy->create($this->creator));
        $this->assertTrue($policy->create($this->specificUserCanRead));
        $this->assertTrue($policy->create($this->specificUserCanWrite));
        $this->assertTrue($policy->create($this->anyUser));
    }

    public function testUpdate(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->update($this->creator, $package));
        $this->assertNull($policy->update($this->specificUserCanRead, $package));
        $this->assertNull($policy->update($this->specificUserCanWrite, $package));
        $this->assertNull($policy->update($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->update($this->creator, $package));
        $this->assertTrue($policy->update($this->specificUserCanRead, $package));
        $this->assertTrue($policy->update($this->specificUserCanWrite, $package));
        $this->assertTrue($policy->update($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->update($this->creator, $package));
        $this->assertNull($policy->update($this->specificUserCanRead, $package));
        $this->assertTrue($policy->update($this->specificUserCanWrite, $package));
        $this->assertNull($policy->update($this->anyUser, $package));
    }

    public function testReplicate(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));
    }

    public function testDelete(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));
    }

    public function testForceDelete(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertFalse($policy->forceDelete($this->creator, $package));
        $this->assertFalse($policy->forceDelete($this->specificUserCanRead, $package));
        $this->assertFalse($policy->forceDelete($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->forceDelete($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertFalse($policy->forceDelete($this->creator, $package));
        $this->assertFalse($policy->forceDelete($this->specificUserCanRead, $package));
        $this->assertFalse($policy->forceDelete($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->forceDelete($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertFalse($policy->forceDelete($this->creator, $package));
        $this->assertFalse($policy->forceDelete($this->specificUserCanRead, $package));
        $this->assertFalse($policy->forceDelete($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->forceDelete($this->anyUser, $package));
    }

    public function testRestore(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->restore($this->creator, $package));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $package));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $package));
        $this->assertFalse($policy->restore($this->anyUser, $package));
    }

    public function testAttachDocument(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->addDocument($this->creator, $package));
        $this->assertNull($policy->addDocument($this->specificUserCanRead, $package));
        $this->assertNull($policy->addDocument($this->specificUserCanWrite, $package));
        $this->assertNull($policy->addDocument($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->addDocument($this->creator, $package));
        $this->assertTrue($policy->addDocument($this->specificUserCanRead, $package));
        $this->assertTrue($policy->addDocument($this->specificUserCanWrite, $package));
        $this->assertTrue($policy->addDocument($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->addDocument($this->creator, $package));
        $this->assertNull($policy->addDocument($this->specificUserCanRead, $package));
        $this->assertTrue($policy->addDocument($this->specificUserCanWrite, $package));
        $this->assertNull($policy->addDocument($this->anyUser, $package));
    }

    public function testAttachUser(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->attachUser($this->creator, $package));
        $this->assertNull($policy->attachUser($this->specificUserCanRead, $package));
        $this->assertNull($policy->attachUser($this->specificUserCanWrite, $package));
        $this->assertNull($policy->attachUser($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->attachUser($this->creator, $package));
        $this->assertNull($policy->attachUser($this->specificUserCanRead, $package));
        $this->assertNull($policy->attachUser($this->specificUserCanWrite, $package));
        $this->assertNull($policy->attachUser($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->attachUser($this->creator, $package));
        $this->assertNull($policy->attachUser($this->specificUserCanRead, $package));
        $this->assertNull($policy->attachUser($this->specificUserCanWrite, $package));
        $this->assertNull($policy->attachUser($this->anyUser, $package));
    }

    public function testDetachUser(): void
    {
        $policy = new PackagePolicy();

        $package = $this->publicPackageToRead;

        $this->assertTrue($policy->detachUser($this->creator, $package));
        $this->assertNull($policy->detachUser($this->specificUserCanRead, $package));
        $this->assertNull($policy->detachUser($this->specificUserCanWrite, $package));
        $this->assertNull($policy->detachUser($this->anyUser, $package));

        $package = $this->publicPackageToWrite;

        $this->assertTrue($policy->detachUser($this->creator, $package));
        $this->assertNull($policy->detachUser($this->specificUserCanRead, $package));
        $this->assertNull($policy->detachUser($this->specificUserCanWrite, $package));
        $this->assertNull($policy->detachUser($this->anyUser, $package));

        $package = $this->protectedPackage;

        $this->assertTrue($policy->detachUser($this->creator, $package));
        $this->assertNull($policy->detachUser($this->specificUserCanRead, $package));
        $this->assertNull($policy->detachUser($this->specificUserCanWrite, $package));
        $this->assertNull($policy->detachUser($this->anyUser, $package));
    }
}
