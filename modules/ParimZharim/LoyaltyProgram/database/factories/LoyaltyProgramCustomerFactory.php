<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\CMS\Domain\Models\Content;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;
use Modules\ParimZharim\Profile\Database\Factories\CustomerFactory;
use Modules\ParimZharim\Profile\Domain\Models\Customer;

// Ensure this is the correct namespace for your Content model

/**
 * @extends Factory<Content>
 */
class LoyaltyProgramCustomerFactory extends CustomerFactory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = LoyaltyProgramCustomer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'discount' => $this->faker->numberBetween(10, 40),
        ]);
    }

}
