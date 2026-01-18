<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;

/**
 * @extends Factory<ServiceCategory>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true) ,
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'product_category_id' => ProductCategory::factory(),
        ];
    }
}
