<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;

/**
 * @extends Factory<ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = ServiceCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true)  // Generate a more realistic category name
        ];
    }
}
