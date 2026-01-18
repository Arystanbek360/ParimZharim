<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shared\CMS\Database\Factories\ContentFactory;
use Modules\Shared\CMS\Domain\Models\Content;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedContentData();
    }

    /**
     * Сидирование тестовых данных для контента.
     */
    protected function seedContentData(): void
    {
        if (!Content::where('slug', 'privacy-policy')->exists()) {
            try {
                ContentFactory::new()->create([
                    'title' => 'Конфиденциальность',
                    'slug' => 'privacy-policy',
                    'content' => join("\n\n", [
                        "1. Определения",
                        "2. Использование информации",
                        "3. Передача информации",
                        "4. Хранение информации",
                        "5. Права и обязанности",
                        "6. Изменения в политике конфиденциальности",
                        "7. Контакты"
                    ]),
                ]);
            } catch (Throwable $e) {
                echo $e->getMessage().PHP_EOL;
            }
        }
    }
}
