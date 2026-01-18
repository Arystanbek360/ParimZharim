<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),  // Generates a realistic service name
            'description' => $this->faker->sentence(10),  // Generates a sentence of 10 words
            'price' => $this->faker->randomFloat(2, 20, 500),  // Generates a random price between 20 and 500
            'service_category_id' => ServiceCategory::factory(),  // Generates a new ServiceCategory and uses its ID
        ];
    }
}
