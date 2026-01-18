<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shared\Payment\Domain\Models\PaymentMethod;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedPaymentMethods();
    }

    /**
     * Сидирование методов платежей.
     */
    protected function seedPaymentMethods(): void
    {
        foreach (PaymentMethodType::cases() as $paymentMethodType) {
            try {
                PaymentMethod::firstOrCreate([
                    'type' => $paymentMethodType->value,
                    'is_available_for_mobile' => true,
                    'is_available_for_admin' => true,
                    'is_available_for_web' => true,
                ]);
            } catch (Throwable $e) {
                echo $e->getMessage() . PHP_EOL; // Improved exception handling with error output
            }
        }
    }
}
