<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shared\ModuleTemplate\Domain\Models\Template;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

/**
 * @extends Factory<Template>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = Payment::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => fake()->numberBetween(1, 10),
            'payment_method' => PaymentMethodType::CASH,
            'total' => fake()->randomFloat(2, 1, 100),
            'status' => PaymentStatus::CREATED,
            'external_id' => fake()->uuid,
            'comment' => fake()->sentence,
            'items' => '[]',
        ];
    }
}
