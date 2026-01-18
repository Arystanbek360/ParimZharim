<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\DocumentFactory;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Services\PackageService;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class PackageServiceTest extends TestCase
{


    public function testCreateNewPackage(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->make(['creator_id' => $user->id]);

        PackageService::savePackage($package);

        $this->assertDatabaseHas('documents_packages', ['id' => $package->id]);
    }

    public function testUpdatePackage(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['creator_id' => $user->id]);

        $package->name = 'Updated Package Name';
        PackageService::savePackage($package);

        $this->assertDatabaseHas('documents_packages', ['id' => $package->id, 'name' => 'Updated Package Name']);
    }

    public function testSaveDocumentInPackage(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['creator_id' => $user->id]);
        $document = $this->makeAndGetTestDocument(['creator_id' => $user->id]);

        PackageService::saveDocumentInPackage($document, $package);

        $this->assertDatabaseHas('documents_documents', ['id' => $document->id, 'package_id' => $package->id]);
    }

    public function testValidateAccessToPackage(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['creator_id' => $user->id]);

        $result = PackageService::validateAccessToPackage($package, $user, AccessType::READ);

        $this->assertTrue($result);
    }
}
