<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Services;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\Coupon;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;
use Modules\ParimZharim\LoyaltyProgram\Domain\Repositories\LoyaltyProgramCustomerRepository;
use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Domain\BaseDomainService;

class LoyaltyProgramService extends BaseDomainService
{

    public static function getLoyaltyProgramCustomerRepository(): LoyaltyProgramCustomerRepository
    {
        return app(LoyaltyProgramCustomerRepository::class);
    }

    public static function calculateDiscountPercentOnDate(Customer $customer, Carbon $date, string $timezone, ?Coupon $coupon = null): array
    {
        $dateOfOrder = $date->copy()->setTimezone($timezone);
        $loyaltyProgramCustomer = self::getLoyaltyProgramCustomerRepository()->getLoyaltyProgramCustomerById($customer->id);
        $discounts = [];

        // Regular discount
        $regularDiscount = $loyaltyProgramCustomer->discount ?? 0.0;
        $discounts['Regular'] = [
            'amount' => $regularDiscount,
            'reason' => "Regular Discount with user ID {$loyaltyProgramCustomer->id}, size of discount: {$regularDiscount}%"
        ];

        // Coupon discount
        $couponDiscount = $coupon ? $coupon->amount : 0.0;
        $discounts['Coupon'] = [
            'amount' => $couponDiscount,
            'reason' => "Coupon Discount with user ID {$loyaltyProgramCustomer->id}, size of discount: {$couponDiscount}%"
        ];

        // Birthday discount
        $birthdayDiscount = 0.0;
        if ($loyaltyProgramCustomer->date_of_birth) {
            $customerBirthday = Carbon::parse($loyaltyProgramCustomer->date_of_birth)->setTimezone($timezone);
            $isBirthdayToday = $dateOfOrder->copy()->startOfDay()->isSameAs('m-d', $customerBirthday->copy()->startOfDay());
            if ($isBirthdayToday) {
                $birthdayDiscount = 10.0;
            }
        }
        $discounts['Birthday'] = [
            'amount' => $birthdayDiscount,
            'reason' => "Birthday Discount with user ID {$loyaltyProgramCustomer->id}, size of discount: {$birthdayDiscount}%"
        ];

        // Determine the highest discount
        $maxDiscountKey = array_reduce(array_keys($discounts), function ($carry, $key) use ($discounts) {
            return $discounts[$key]['amount'] > $discounts[$carry]['amount'] ? $key : $carry;
        }, 'Regular');

        $maxDiscount = $discounts[$maxDiscountKey]['amount'];
        $discountReason = $discounts[$maxDiscountKey]['reason'];

        if ($maxDiscount == 0.0) {
            $discountReason = "";
        }

        return [
            'discount' => $maxDiscount,
            'reason' => $discountReason
        ];
    }

    public static function getCurrentAndNextCustomerDiscount(LoyaltyProgramCustomer $customer): array
    {
        $customerOrderTotal = OrderService::getCustomerTotalOrdersAmount($customer->id);
        $currentDiscount = $customer->discount;
        $availableDiscounts = self::getLoyaltyProgramCustomerRepository()->getAvailableDiscountTiersForCustomer($currentDiscount, $customerOrderTotal);
        $nextDiscount = $availableDiscounts->first();
        $nextDiscountThreshold = $nextDiscount ? (int)$nextDiscount->threshold_amount : 0;
        $needToNextDiscount = (int) $nextDiscountThreshold - (int)$customerOrderTotal;

        return [
            'current_discount' => $currentDiscount,
            'next_discount' => $nextDiscount ? $nextDiscount->discount_percentage : null,
            'next_discount_from' => $nextDiscountThreshold,
            'need_to_next_discount' => $needToNextDiscount,
            'total_order_amount' => $customerOrderTotal,
        ];
    }

    public static function recalculateDiscountForCustomer(LoyaltyProgramCustomer $customer): void
    {
        // Получаем общую сумму заказов для клиента
        $totalOrderAmount = OrderService::getCustomerTotalOrdersAmount($customer->id);

        // Получаем максимальную доступную скидку для этой суммы через метод репозитория
        $maxApplicableDiscount = self::getLoyaltyProgramCustomerRepository()->getApplicableDiscountForTotalOrderAmount($totalOrderAmount);

        // Сравниваем текущую скидку и рассчитанную
        if ($maxApplicableDiscount !== null && $maxApplicableDiscount > $customer->discount) {
            // Назначаем скидку только если она больше текущей
            $customer->discount = $maxApplicableDiscount;
            self::getLoyaltyProgramCustomerRepository()->save($customer);
        }
    }
}
