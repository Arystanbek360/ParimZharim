<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ParimZharim\ProductsAndServices\Database\Factories\ProductCategoryFactory;
use Modules\ParimZharim\ProductsAndServices\Database\Factories\ProductFactory;
use Modules\ParimZharim\ProductsAndServices\Database\Factories\ServiceCategoryFactory;
use Modules\ParimZharim\ProductsAndServices\Database\Factories\ServiceFactory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedServiceCategories();
        $this->seedProductCategories();
    }

    /**
     * Seed service categories and services.
     */
    protected function seedServiceCategories(): void
    {
        if (ServiceCategory::count() > 0) {
            return; // Exit if categories already exist.
        }

        $serviceCategories = [
            'Штрафы',
            'Дополнительные услуги'
        ];

        foreach ($serviceCategories as $categoryName) {
            $category = ServiceCategoryFactory::new()->create(['name' => $categoryName]);

            $services = [
                ['name' => 'Штраф за опоздание', 'price' => 5000],
                ['name' => 'Дополнительный час работы', 'price' => 10000],
            ];

            foreach ($services as $service) {
                ServiceFactory::new()->create([
                    'name' => $service['name'],
                    'description' => 'Описание для ' . $service['name'],
                    'price' => $service['price'],
                    'service_category_id' => $category->id
                ]);
            }
        }
    }

    /**
     * Seed product categories and products.
     */
    protected function seedProductCategories(): void
    {
        $productCategoryNames = ['Хит продаж', 'Салаты', 'Супы'];

        foreach ($productCategoryNames as $categoryName) {
            $category = ProductCategoryFactory::new()->create(['name' => $categoryName]);

            $products = [
                ['name' => 'Микс мясной на компанию', 'price' => 26000],
                ['name' => 'Нисуаз с тунцом, 300 гр', 'price' => 2900],
                ['name' => 'Рамен с телятиной, 600 гр', 'price' => 1900],
            ];

            foreach ($products as $product) {
                ProductFactory::new()->create([
                    'name' => $product['name'],
                    'description' => 'Описание для ' . $product['name'],
                    'price' => $product['price'],
                    'product_category_id' => $category->id
                ]);
            }
        }
    }
}
