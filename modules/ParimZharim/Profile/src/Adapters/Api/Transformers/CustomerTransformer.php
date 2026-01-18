<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Adapters\Api\Transformers;

use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class CustomerTransformer extends BaseTransformer
{

    private function getRandomDiscount(): array
    {
        $discountValues = [0, 3, 5, 7, 10];
        $currentDiscount = $discountValues[array_rand($discountValues)];

        if ($currentDiscount === 10) {
            return [
                'current_discount' => $currentDiscount,
                'next_discount' => null,
                'next_discount_from' => null,
            ];
        }

        $nextDiscount = match ($currentDiscount) {
            0 => 3,
            3 => 5,
            5 => 7,
            default => 10,
        };
        $nextDiscountFrom = rand(10000, 50000);  // Example range, adjust as needed

        return [
            'current_discount' => $currentDiscount,
            'next_discount' => $nextDiscount,
            'next_discount_from' => $nextDiscountFrom + 30000,
        ];
    }

    public function transform(Customer|BaseDTO|BaseValueObject|BaseModel|array $data): array
    {
        $discountInfo = $this->getRandomDiscount();

        return [
            'customer_id' => $data->id,
            'user_id' => $data->user_id,
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'dateOfBirth' => $data->date_of_birth ? $data->date_of_birth->format('Y-m-d') : null,
        ];
    }
}
