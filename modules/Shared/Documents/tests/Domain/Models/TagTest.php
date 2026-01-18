<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Tests\TestCase;

class TagTest extends TestCase
{


    private const string TABLE = 'documents_tags';
    private const string TEST_NAME = 'Тестовый Tag';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Проверка: Создается валидный экземпляр модели с обязательными атрибутами.
     * @return void
     */
    public function testCreateModel(): void
    {
        $name = self::TEST_NAME;
        $model = new Tag();
        $model->name = $name;
        $this->assertSame($name, $model->name);
    }

    /**
     * Проверка: Создается и сохраняется в БД валидный экземпляр модели с обязательными атрибутами и временными штампами.
     * @return void
     */
    public function testSaveModel(): void
    {
        $testName = self::TEST_NAME;
        $model = new Tag(['name' => $testName]);

        $this->assertDatabaseMissing(self::TABLE,
            [
                'name' => $testName,
            ]);

        $model->save();
        $this->assertNotEmpty($model->created_at);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $testName,
                'created_at' => $model->created_at,
            ]);
    }

    /**
     * Проверка: Модель корректно обновляется в БД.
     * @return void
     */
    public function testUpdateModel(): void
    {
        $testName_1 = self::TEST_NAME;
        $testName_2 = 'updated ' . $testName_1;
        $model = new Tag(['name' => $testName_1]);
        $model->save();

        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $testName_1,
            ]);
        $model->name = $testName_2;
        $model->update();
        $this->assertNotEmpty($model->updated_at);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $testName_2,
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ]);
    }

    /**
     * Проверка: Модель мягко удаляется в БД.
     * @return void
     */
    public function testSoftDeleteModel(): void
    {
        $testName = self::TEST_NAME;
        $model = new Tag(['name' => $testName]);
        $model->save();

        $this->assertNull($model->deleted_at);;
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $testName,
                'created_at' => $model->created_at,
                'deleted_at' => null,
            ]);
        $model->deleted_at = Carbon::now();
        $model->save();
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $model->id,
                'name' => $testName,
                'created_at' => $model->created_at,
                'deleted_at' => $model->deleted_at,
            ]);
    }

    /**
     * Проверка: Фабрика модели, вызванная через класс модели, корректно создает валидные экземпляры.
     * @return void
     */
    public function testModelsFactory(): void
    {
        $testName = self::TEST_NAME;
        $model = Tag::factory()->create(['name' => $testName]);

        $this->assertNotEmpty($model->created_at);
        $this->assertDatabaseHas('documents_tags',
            [
                'id' => $model->id,
                'name' => $testName,
                'created_at' => $model->created_at,
            ]);
    }
}
