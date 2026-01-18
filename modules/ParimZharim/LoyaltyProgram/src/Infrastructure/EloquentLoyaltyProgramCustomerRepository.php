<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Infrastructure;

use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTier;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTierCollection;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;
use Modules\ParimZharim\LoyaltyProgram\Domain\Repositories\LoyaltyProgramCustomerRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;

class EloquentLoyaltyProgramCustomerRepository extends BaseRepository implements LoyaltyProgramCustomerRepository
{

    public function getLoyaltyProgramCustomerById(int $customerId): ?LoyaltyProgramCustomer
    {
        return LoyaltyProgramCustomer::find($customerId);
    }

    public function getAvailableDiscountTiersForCustomer(int $currentDiscount, float $customerOrderTotal): DiscountTierCollection
    {
        $availableDiscounts = DiscountTier::where('discount_percentage', '>', $currentDiscount)
            ->where('start_date', '<=', now())
            ->orderBy('discount_percentage')
            ->get();

        return new DiscountTierCollection($availableDiscounts);
    }

    public function getApplicableDiscountForTotalOrderAmount(float $totalOrderAmount): ?int
    {
        // Получаем актуальные скидки, которые действуют на данный момент (проверка по start_date)
        $applicableDiscount = DiscountTier::where('threshold_amount', '<=', $totalOrderAmount) // Смотрим, какие скидки подходят по сумме
        ->where('start_date', '<=', now()) // Проверяем, чтобы скидка уже начала действовать
        ->orderBy('discount_percentage', 'desc') // Сортируем по убыванию скидки, чтобы взять максимальную
        ->first(); // Берём первую (максимальную) скидку

        // Возвращаем скидку, если она найдена, иначе null
        return $applicableDiscount?->discount_percentage;
    }

    public function save(LoyaltyProgramCustomer $customer): void
    {
        $customer->save();
    }
}
