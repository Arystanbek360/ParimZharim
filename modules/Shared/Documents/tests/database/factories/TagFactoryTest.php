<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Database\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\TagFactory;
use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Tests\TestCase;

class TagFactoryTest extends TestCase
{


    // Тестовые данные
    private const string TABLE = 'documents_tags';
    private array $testAttributes = ['name' => "Test Тег"];

    /**
     * Проверка: Фабрика создает валидный экземпляр с обязательными  полями.
     * @return void
     */
    public function testMakeInstance(): void
    {
        $instance = TagFactory::new()->make();
        $this->assertNotNull($instance->name);
    }

    /**
     * Проверка: Фабрика создает валидный экземпляр и заполняет обязательные поля значениями по-умолчанию.
     * @return void
     */
    public function testMakeInstanceDefaults(): void
    {
        $instance = TagFactory::new()->make();
        $this->assertNotEmpty($instance->name);
        echo "\nname: $instance->name;\n";
    }

    /**
     * Проверка: Фабрика создает валидный экземпляр с переданными атрибутами.
     * @return void
     */
    public function testMakeInstanceWithAttributes(): void
    {
        $instance = TagFactory::new()->make($this->testAttributes);
        $this->assertSame($this->testAttributes['name'], $instance->name);
    }

    /**
     * Проверка: Фабрика создает и сохраняет валидные экземпляры в базе данных.
     * @return void
     */
    public function testDatabaseHasInstance(): void
    {
        $instance_1 = TagFactory::new()->create($this->testAttributes);
        $instance_2 = TagFactory::new()->create();

        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $instance_1->id,
                'name' => $this->testAttributes['name'],
            ]);

        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $instance_2->id,
                'name' => $instance_2->name,
            ]);
    }

    /**
     * Проверка: Фабрика создает несколько валидных экземпляров.
     * @return void
     */
    public function testCreateMultiplyInstances(): void
    {
        $instances = TagFactory::new()->count(10)->create();
        $this->assertNotEmpty($instances);

        /** @var Tag $instance */
        foreach ($instances as $instance) {
            $this->assertDatabaseHas(self::TABLE,
                [
                    'id' => $instance->id,
                    'name' => $instance->name,
                ]);
        }
    }
}
