<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Models\PackageToUserPivot;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class PackageToUserPivotTest extends TestCase
{


    private const string TABLE = 'documents_package_to_user';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Проверка: Создается валидный экземпляр pivot-модели с обязательными атрибутами.
     * @return void
     */
    public function testCreateModel(): void
    {
        $package = Package::factory()->create();
        $packageID = $package->id;
        $userID = User::factory()->create()->id;

        $pivot = new PackageToUserPivot(['package_id' => $packageID, 'user_id' => $userID]);

        $this->assertSame($packageID, $pivot->package_id);
        $this->assertSame($userID, $pivot->user_id);
    }

    /**
     * Проверка: Сохраняется запись в таблице с заданными атрибутами.
     * @return void
     */
    public function testSaveModel(): void
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();

        $pivot = new PackageToUserPivot([
            'package_id' => $package->id,
            'user_id' => $user->id,
            'access_type' => AccessType::READ,
        ]);

        $this->assertDatabaseMissing(self::TABLE, [
            'package_id' => $package->id,
            'user_id' => $user->id,
        ]);

        $pivot->save();

        $this->assertNotEmpty($pivot->created_at);
        $this->assertDatabaseHas(self::TABLE, [
            'package_id' => $package->id,
            'user_id' => $user->id,
            'access_type' => AccessType::READ,
        ]);
    }

    /**
     * Проверка: Сохраняется запись в таблице с заданными атрибутами.
     * @return void
     */
    public function testChangeAccessType(): void
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();

        $pivot = new PackageToUserPivot([
            'package_id' => $package->id,
            'user_id' => $user->id,
            'access_type' => AccessType::COMMENT,
        ]);

        $pivot->save();

        $this->assertDatabaseHas(self::TABLE, [
            'package_id' => $package->id,
            'user_id' => $user->id,
            'access_type' => AccessType::COMMENT,
        ]);

        $package->users()->updateExistingPivot($user->id, ['access_type' => AccessType::WRITE]);

        $this->assertDatabaseHas(self::TABLE, [
            'package_id' => $package->id,
            'user_id' => $user->id,
            'access_type' => AccessType::WRITE,
        ]);
    }
}
