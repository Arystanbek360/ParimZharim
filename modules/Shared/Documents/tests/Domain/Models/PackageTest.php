<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Database\Factories\UserFactory;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class PackageTest extends TestCase
{


    // Тестовые данные
    protected const string TABLE = 'documents_packages';

    protected string $testName = "test ПАкеТ";
    protected string $testType = "test Тип";
    protected string $testStatus = "test СтАтУс";
    protected array $testMetadata = ['meta' => "data"];
    protected AccessMode $testAccessMode = AccessMode::SPECIFIC_USERS;
    protected AccessType $defaultAccessType = AccessType::READ;
    protected int $testCreatorId;
    protected array $testAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->testCreatorId = UserFactory::new()->create()->id;

        $this->testAttributes =
            [
                'name' => $this->testName,
                'type' => $this->testType,
                'status' => $this->testStatus,
                'creator_id' => $this->testCreatorId,
                'parent_package_id' => null,
                'metadata' => $this->testMetadata,
                'access_mode' => $this->testAccessMode,
            ];
    }

    /**
     * Проверка: Создается валидный экземпляр модели с обязательными атрибутами.
     * @return void
     */
    public function testCreateModel(): void
    {
        $model = new Package($this->testAttributes);

        $this->assertSame($this->testName, $model->name);
        $this->assertSame($this->testType, $model->type);
        $this->assertSame($this->testStatus, $model->status);
        $this->assertSame($this->testCreatorId, $model->creator_id);
        $this->assertSame($this->testMetadata, $model->metadata);
        $this->assertSame($this->testAccessMode, $model->access_mode);
        $this->assertSame($this->defaultAccessType, $model->default_access_type);
    }

    /**
     * Проверка: Создается и сохраняется в БД валидный экземпляр модели с обязательными атрибутами и временными штампами.
     * @return void
     */
    public function testSaveModel(): void
    {
        $model = new Package($this->testAttributes);
        //print_r("АТРИБУТЫ ПАКЕТА:\n" . json_encode($model->getAttributes()) . "\n");

        $this->assertDatabaseMissing(self::TABLE,
            [
                'name' => $this->testName,
            ]);

        $model->save();

        $this->assertNotEmpty($model->created_at);
        $this->assertDatabaseHas('idm_users', ['id' => $model->creator_id]);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $this->testName,
                'type' => $this->testType,
                'status' => $this->testStatus,
                'creator_id' => $this->testCreatorId,
                'metadata' => json_encode($this->testMetadata),
                'access_mode' => $this->testAccessMode->value,
                'default_access_type' => $this->defaultAccessType->value,
                'created_at' => $model->created_at,
            ]);
    }

    /**
     * Проверка: Модель корректно обновляется в БД.
     * @return void
     */
    public function testUpdateModel(): void
    {
        $testName_1 = $this->testName;
        $testName_2 = 'updated ' . $testName_1;
        $model = new Package($this->testAttributes);
        $model->save();
        $this->assertEquals($model->created_at, $model->updated_at);

        sleep(1);

        $model->name = $testName_2;
        $model->update();
        $this->assertNotEquals($model->created_at, $model->updated_at);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $testName_2,
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ]);
        //echo  $model->updated_at;
    }

    /**
     * Проверка: Модель мягко удаляется в БД.
     * @return void
     */
    public function testSoftDeleteModel(): void
    {
        $model = new Package($this->testAttributes);
        $model->save();

        $this->assertEmpty($model->deleted_at);;
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $this->testName,
                'created_at' => $model->created_at,
                'deleted_at' => null,
            ]);
        $model->deleted_at = Carbon::now();
        $model->save();
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $this->testName,
                'created_at' => $model->created_at,
                'deleted_at' => $model->deleted_at,
            ]);
    }

    /**
     * Проверка: Связь модели пакета с моделью пользователя корректно работает.
     * @return void
     */
    public function testUsersRelationship(): void
    {
        $package = new Package($this->testAttributes);
        $package->save();

        $user = UserFactory::new()->create();

        $attachedUser = $package->users()->find($user->id);
        $this->assertNull($attachedUser);

        $package->users()->attach($user->id);

        /** @var User $attachedUser */
        $attachedUser = $package->users()->find($user->id);
        $this->assertNotNull($attachedUser);
        $this->assertSame($user->id, $attachedUser->id);
        $this->assertSame($user->name, $attachedUser->name);
        $this->assertSame($package->default_access_type, $attachedUser->pivot->access_type);
    }

    /**
     * Проверка: Фабрика модели, вызванная через класс модели, корректно создает валидные экземпляры.
     * @return void
     */
    public function testModelsFactory(): void
    {
        $model = Package::factory()->create($this->testAttributes);

        $this->assertNotEmpty($model->created_at);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $this->testName,
                'type' => $this->testType,
                'status' => $this->testStatus,
                'creator_id' => $this->testCreatorId,
                'metadata' => json_encode($this->testMetadata),
                'access_mode' => $this->testAccessMode->value,
                'default_access_type' => $this->defaultAccessType->value,
                'created_at' => $model->created_at,
            ]);
    }
}
