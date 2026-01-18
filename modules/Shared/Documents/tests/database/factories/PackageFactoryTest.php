<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Database\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\PackageFactory;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Database\Factories\UserFactory;

class PackageFactoryTest extends TestCase
{


    // Тестовые данные
    private const string TABLE = 'documents_packages';
    private const string USERS_TABLE = 'idm_users';
    private array $testAttributes = [];

    // Определение тестовых данных
    protected function setUp(): void
    {
        parent::setUp();

        $this->testAttributes =
            [
                'name' => "Test-Пакет",
                'status' => "Test_Статус",
                'type' => "TestТип",
                'creator_id' => UserFactory::new()->create()->id,
                'metadata' => ['TestData' => 'Meta*data', 'Disclaimer' => 'Запрещеная организация'],
                'access_mode' => AccessMode::ANY_USER,
                'default_access_type' => AccessType::COMMENT,
            ];
    }

    /**
     * Проверка: Фабрика создает валидный экземпляр с обязательными полями.
     * @return void
     */
    public function testMakeInstance(): void
    {
        $instance = PackageFactory::new()->make();

        $this->assertNotNull($instance->name);
        $this->assertNotNull($instance->type);
        $this->assertNotNull($instance->status);
        $this->assertNotNull($instance->creator_id);
        $this->assertNotNull($instance->metadata);
        $this->assertNotNull($instance->access_mode);
        $this->assertNotNull($instance->default_access_type);
    }

    /**
     * Проверка: Фабрика создает валидный экземпляр и заполняет обязательные поля значениями по-умолчанию.
     * @return void
     */
    public function testMakeInstanceDefaults(): void
    {
        $instance = PackageFactory::new()->make();

        $this->assertNotEmpty($instance->name);
        $this->assertNotEmpty($instance->type);
        $this->assertNotEmpty($instance->status);
        $this->assertNotEmpty($instance->creator_id);
        $this->assertNotEmpty($instance->metadata);
        $this->assertNotEmpty($instance->access_mode);
        $this->assertNotEmpty($instance->default_access_type);

        $this->assertDatabaseHas(self::USERS_TABLE, ['id' => $instance->creator_id]);

//        // Вывод для контроля назначенных дефолтных значений
//        $metadata = json_encode($instance->metadata, JSON_UNESCAPED_UNICODE);
//        $accessMode = $instance->access_mode->value;
//        $defaultAccessType = $instance->default_access_type->value;

//        echo "name: $instance->name; type: $instance->type; status: $instance->status; creator_id: $instance->creator_id;
//        metadata: $metadata; access_mode: $accessMode; access_type: $defaultAccessType;";
    }

    /**
     * Проверка: Фабрика создает валидный экземпляр с переданными атрибутами.
     * @return void
     */
    public function testMakeInstanceWithAttributes(): void
    {
        $attributes = $this->testAttributes;

        $testName = $this->testAttributes['name'];
        $testType = $this->testAttributes['type'];
        $testStatus = $this->testAttributes['status'];
        $testCreatorId = $this->testAttributes['creator_id'];
        $testMetadata = $this->testAttributes['metadata'];
        $testAccessMode = $this->testAttributes['access_mode'];
        $testAccessType = $this->testAttributes['default_access_type'];

        $instance = PackageFactory::new()->make($attributes);

        $this->assertSame($testName, $instance->name);
        $this->assertSame($testType, $instance->type);
        $this->assertSame($testStatus, $instance->status);
        $this->assertSame($testCreatorId, $instance->creator_id);
        $this->assertEquals($testMetadata, $instance->metadata);
        $this->assertEquals($testAccessMode, $instance->access_mode);
        $this->assertEquals($testAccessType, $instance->default_access_type);
    }

    /**
     * Проверка: Фабрика создает и сохраняет валидные экземпляры в базе данных.
     * @return void
     */
    public function testDatabaseHasInstance(): void
    {
        $attributes = $this->testAttributes;

        $testName = $this->testAttributes['name'];
        $testType = $this->testAttributes['type'];
        $testStatus = $this->testAttributes['status'];
        $testCreatorId = $this->testAttributes['creator_id'];
        $testMetadata = $this->testAttributes['metadata'];
        $testAccessMode = $this->testAttributes['access_mode'];
        $testAccessType = $this->testAttributes['default_access_type'];

        $instance_1 = PackageFactory::new()->create($attributes);
        $instance_2 = PackageFactory::new()->create();

        $this->assertDatabaseHas(self::USERS_TABLE, ['id' => $testCreatorId]);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $instance_1->id,
                'name' => $testName,
                'type' => $testType,
                'status' => $testStatus,
                'creator_id' => $testCreatorId,
                'metadata' => json_encode($testMetadata),
                'access_mode' => $testAccessMode->value,
                'default_access_type' => $testAccessType->value,
            ]);

        $this->assertDatabaseHas(self::USERS_TABLE, ['id' => $instance_2->creator_id]);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $instance_2->id,
                'name' => $instance_2->name,
                'type' => $instance_2->type,
                'status' => $instance_2->status,
                'creator_id' => $instance_2->creator_id,
                'metadata' => json_encode($instance_2->metadata),
                'access_mode' => $instance_2->access_mode->value,
                'default_access_type' => $instance_2->default_access_type->value,
            ]);
    }

    /**
     * Проверка: Фабрика создает несколько валидных экземпляров.
     * @return void
     */
    public function testCreateMultiplyInstances(): void
    {
        $instances = PackageFactory::new()->count(10)->create();
        $this->assertNotEmpty($instances);

        /** @var Package $instance */
        foreach ($instances as $instance) {
            $this->assertDatabaseHas(self::TABLE,
                [
                    'id' => $instance->id,
                    'name' => $instance->name,
                    'type' => $instance->type,
                    'status' => $instance->status,
                    'creator_id' => $instance->creator_id,
                    'metadata' => json_encode($instance->metadata),
                    'access_mode' => $instance->access_mode->value,
                    'default_access_type' => $instance->default_access_type->value,
                ]);
        }
    }

    /**
     * Кейс: Фабрика создает валидный экземпляр пакета с назначением родительского пакета.
     * @return void
     */
    public function testInstancesParentPackage(): void
    {
        $instance_1 = PackageFactory::new()->create();
        $this->assertEquals(null, $instance_1->parent_package_id);

        $instance_2 = PackageFactory::new()->create(['parent_package_id' => $instance_1->id]);

        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $instance_2->parent_package_id,
                'parent_package_id' => null,
            ]);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $instance_2->id,
                'parent_package_id' => $instance_1->id,
            ]);
    }
}
