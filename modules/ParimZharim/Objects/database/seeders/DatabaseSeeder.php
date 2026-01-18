<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Domain\Models\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedCategoriesAndObjects();
    }

    /**
     * Seed categories and service objects.
     */
    protected function seedCategoriesAndObjects(): void
    {
        if (Category::count() > 0) {
            return; // Exit if categories already exist.
        }

        $this->createCategoryWithObjects('Гриль-Домики', 'На карте отмечены местоположения гриль-домиков, где посетители могут приготовить еду на гриле и провести время на свежем воздухе.', 4);
        $this->createCategoryWithObjects('Бани', 'Разные типы бань, обозначены на карте.', 6);
        $this->seedTagsAndAssignToObjects();
    }

    /**
     * Create a category and its associated service objects.
     */
    protected function createCategoryWithObjects($name, $description, $count): void
    {
        $category = Category::factory()->create([
            'name' => $name,
            'description' => $description,
        ]);

        for ($i = 1; $i <= $count; $i++) {
            ServiceObject::factory()->create([
                'name' => "{$name} №$i",
                'category_id' => $category->id,
                'description' => "Описание {$name}",
                'capacity' => rand(5, 20), // Randomize capacity based on object type
            ]);
        }
    }

    /**
     * Seed tags and assign them to all service objects.
     */
    protected function seedTagsAndAssignToObjects(): void
    {
        $tags = ['Камин', 'Бар', 'Караоке', 'Сауна'];

        foreach ($tags as $tagName) {
            Tag::factory()->create(['name' => $tagName]);
        }

        $tagIds = Tag::all()->pluck('id')->toArray();

        ServiceObject::all()->each(function ($serviceObject) use ($tagIds) {
            $serviceObject->tags()->attach($tagIds);
        });
    }
}
