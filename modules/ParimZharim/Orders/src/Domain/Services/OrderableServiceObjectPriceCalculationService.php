<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Services;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\Coupon;
use Modules\ParimZharim\LoyaltyProgram\Domain\Services\LoyaltyProgramService;
use Modules\ParimZharim\Orders\Domain\Errors\PlanNotFound;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Plan;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\PlanType;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Domain\BaseDomainService;

class OrderableServiceObjectPriceCalculationService extends BaseDomainService {


    public static function calculatePriceAndMetadata(OrderableServiceObject $serviceObject, Carbon $startTime, Carbon $endTime, Customer $customer, int $guests, ?Coupon $coupon = null): array
    {
        $plan = self::getCurrentPlanOrFail ($serviceObject,  $startTime);

        $priceDetails = self::getPriceCalculationByPlan($plan, $serviceObject, $startTime, $endTime, $guests);

        $totalPrice = self::calculateTotalPriceByTimeAndGuests($priceDetails);

        $discountPrice = self::calculateDiscountByTimeAndGuests($serviceObject, $startTime, $customer, $priceDetails, $coupon);

        $maxPrice = 0;
        foreach ($priceDetails['by_time'] as $priceDetail) {
            if ($priceDetail['price'] > $maxPrice) {
                $maxPrice = $priceDetail['price'];
            }
        }

        //advance Payment cannot be more than (total price - discount)
        if ($maxPrice > $totalPrice - $discountPrice) {
            $maxPrice = $totalPrice - $discountPrice;
        }

        return [
            'planRules' => $plan->metadata['rules'],
            'priceDetails' => $priceDetails,
            'totalObjectBookingPrice' => $totalPrice,
            'advancePayment' => $maxPrice,
            'discountPrice' => $discountPrice,
            'discount' => self::getDiscountPercent($serviceObject, $startTime, $customer, $coupon),
        ];

    }

    public static function getDiscountData(OrderableServiceObject $serviceObject, Carbon $startTime, Customer $customer, ?Coupon $coupon): array
    {
        $plan = self::getCurrentPlanOrFail($serviceObject, $startTime);
        if ($plan->plan_type == PlanType::FIXED) {
            return [
                'discount' => 0.0,
                'reason' => "Никакая скидка не применяется, если план фиксированный"
            ];
        }
        return LoyaltyProgramService::calculateDiscountPercentOnDate($customer, $startTime, $serviceObject->getObjectTimezone(), $coupon);
    }

    // TODO: сделать строгую типизацию для $rules и $priceDetails

    // структура JSON поля rules у Plan для документации
    // {
    //   "week_days": [
    //      {"price": "10000", "time_to": "14:00", "weekdays": ["2", "4"], "time_from": "12:00"},
    //      {"price": "50001", "time_to": "19:00", "weekdays": ["5", "6"], "time_from": "12:00"},
    //      {"price": "15000", "time_to": "14:00", "weekdays": ["7"], "time_from": "12:00"}
    //    ],
    //    "concrete_days": [
    //       {"days": ["26-04-2024", "27-04-2024"], "price": "30000", "time_to": "21:00", "time_from": "18:00"},
    //    ],
    //    "guest_limit": {"count": "10", "extra_guest_fee": "4000"}
    // }

    private static function getCurrentPlanOrFail(OrderableServiceObject $serviceObject, Carbon $startTime): Plan
    {
        $dateFrom = $startTime->copy()->setTimezone($serviceObject->getObjectTimezone());

        $plan = $serviceObject->plans()
            ->wherePivot('date_from', '<=', $dateFrom)
            ->orderBy('date_from', 'desc')
            ->first();

        if (!$plan) {
            throw new PlanNotFound();
        }

        return $plan;
    }

    private static function getPriceCalculationByPlan(Plan $plan, OrderableServiceObject $serviceObject,  Carbon $startTime, Carbon $endTime, int $guests): array
    {
        $rules = $plan->metadata['rules'];
        $dateFrom = $startTime->copy()->setTimezone($serviceObject->getObjectTimezone());
        $dateTo = $endTime->copy()->setTimezone($serviceObject->getObjectTimezone());

        return [
            'by_time' => self::calculatePriceByTime($serviceObject, $plan, $rules, $dateFrom, $dateTo),
            'by_guests' => self::calculatePriceByGuests($rules, $guests)
        ];
    }

    /**
     * @throws PlanNotFound
     */
    private static function calculatePriceByTime(OrderableServiceObject $serviceObject, Plan $plan, array $rules, Carbon $dateFrom, Carbon $dateTo): array
    {
        if ($plan->plan_type == PlanType::HOURLY) {
            $priceCalculationByTime = self::calculateHourlyPrice($serviceObject, $rules, $dateFrom, $dateTo);
        } elseif ($plan->plan_type == PlanType::FIXED) {
            $priceCalculationByTime = self::calculateFixedPrice($serviceObject, $rules, $dateFrom, $dateTo);
        } else {
            throw new PlanNotFound('Неверный тип тарифа');
        }

        return $priceCalculationByTime;
    }

    private static  function calculateHourlyPrice(OrderableServiceObject $serviceObject, array $rules, Carbon $dateFrom, Carbon $dateTo): array
    {
        $priceCalculationByTime = [];

        // Получаем количество минут в первом часу и в последнем
        $dateFromMinutesCount = $dateFrom->minute;
        $dateToMinutesCount = $dateTo->minute;

        // для итерации всегда используем дату старта от начала часа
        $dateFromForIteration = $dateFrom->copy()->startOfHour();

        // если $dateTo заканчивается не ровно в час, то приводим к началу следующего часа
        $dateToForIteration = $dateTo->copy();
        if ($dateToForIteration->minute !== 0 || $dateToForIteration->second !== 0) {
            $dateToForIteration = $dateToForIteration->addHour()->startOfHour();
        }

        // Получаем количество часов между датами для итерации
        $hours = $dateFromForIteration->diffInHours($dateToForIteration);

        // Итерируемся по каждому часу
        for ($i = 0; $i < $hours; $i++) {
            // получаем очередной час для итерации
            $currentHour = $dateFromForIteration->copy()->addHours($i);

            $found = false;

            // если первый или последний час, то учитываем минуты для стоимости
            $priceRatio = 1;
            if ($i == 0) {
                $priceRatio = (60 - $dateFromMinutesCount) / 60;
            } elseif ($i == $hours - 1) {
                $priceRatio = (60 - $dateToMinutesCount) / 60;
            }

            if (isset($rules['concrete_days'])) {
                $found = self::applyConcreteDayRules($serviceObject, $rules['concrete_days'], $currentHour, $priceCalculationByTime, $priceRatio);
            }

            if (!$found && isset($rules['week_days'])) {
                $found = self::applyWeekDayRules($rules['week_days'], $currentHour, $priceCalculationByTime, $priceRatio);
            }

            if (!$found) {
                $priceCalculationByTime[] = [
                    'date' => $currentHour->format('Y-m-d H:i:s'),
                    'price' => 0,
                    'description' => "Тариф не найден"
                ];
            }
        }
        return $priceCalculationByTime;
    }

    private static function applyConcreteDayRules(OrderableServiceObject $serviceObject, array $concreteDays, Carbon $currentHour, array &$priceCalculationByTime, float $priceRatio): bool
    {
        foreach ($concreteDays as $day) {
            foreach ($day['days'] as $d) {
                $dayRange = Carbon::parse($d)->setTimezone($serviceObject->getObjectTimezone());
                if ($currentHour->isSameDay($dayRange)) {
                    if ($currentHour->between($dayRange->copy()->setTimeFromTimeString($day['time_from']), $dayRange->copy()->setTimeFromTimeString($day['time_to']))) {
                        $priceCalculationByTime[] = [
                            'date' => $currentHour->format('Y-m-d H:i:s'),
                            'price' => (float)$day['price'] * $priceRatio,
                            'description' => "Цена за " . $dayRange->format('d.m.Y') . " с " . $day['time_from'] . " по " . $day['time_to'] . " (коэффициент цены " . $priceRatio . ")"
                        ];
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function applyWeekDayRules(array $weekDays, Carbon $currentHour, array &$priceCalculationByTime, float $priceRatio): bool
    {
        foreach ($weekDays as $weekDay) {
            if (in_array($currentHour->dayOfWeekIso, $weekDay['weekdays']) &&
                $currentHour->between($currentHour->copy()->setTimeFromTimeString($weekDay['time_from']), $currentHour->copy()->setTimeFromTimeString($weekDay['time_to']))) {
                $priceCalculationByTime[] = [
                    'date' => $currentHour->format('Y-m-d H:i:s'),
                    'price' => (float)$weekDay['price'] * $priceRatio,
                    'description' => "Цена за " . $currentHour->locale('ru')->dayName . " с " . $weekDay['time_from'] . " по " . $weekDay['time_to'] . " (коэффициент цены " . $priceRatio . ")"
                ];
                return true;
            }
        }
        return false;
    }

    private static function calculateFixedPrice(OrderableServiceObject $serviceObject, array $rules, Carbon $dateFrom, Carbon $dateTo): array
    {
        $priceCalculationByTime = [];

        $found = false;

        if (isset($rules['concrete_days'])) {
            $found = self::applyConcreteDayRulesForFixed($serviceObject, $rules['concrete_days'], $dateFrom, $dateTo, $priceCalculationByTime);
        }

        if (!$found && isset($rules['week_days'])) {
            $found = self::applyWeekDayRulesForFixed($rules['week_days'], $dateFrom, $dateTo, $priceCalculationByTime);
        }

        if (!$found) {
            $priceCalculationByTime[] = [
                'date' => $dateFrom->format('Y-m-d H:i:s') . ' - ' . $dateTo->format('Y-m-d H:i:s'),
                'price' => 0,
                'description' => "Тариф не найден"
            ];
        }

        return $priceCalculationByTime;
    }

    private static function applyConcreteDayRulesForFixed(OrderableServiceObject $serviceObject, array $concreteDays, Carbon $dateFrom, Carbon $dateTo, array &$priceCalculationByTime): bool
    {
        foreach ($concreteDays as $day) {
            foreach ($day['days'] as $d) {
                $dayRange = Carbon::parse($d)->setTimezone($serviceObject->getObjectTimezone());
                if ($dateFrom->isSameDay($dayRange)) {
                    $priceCalculationByTime[] = [
                        'date' => $dateFrom->format('Y-m-d H:i:s') . ' - ' . $dateTo->format('Y-m-d H:i:s'),
                        'price' => (float)$day['price'],
                        'description' => "Фиксированная цена для " . $dayRange->format('d.m.Y')
                    ];
                    return true;
                }
            }
        }
        return false;
    }

    private static function applyWeekDayRulesForFixed(array $weekDays, Carbon $dateFrom, Carbon $dateTo, array &$priceCalculationByTime): bool
    {
        foreach ($weekDays as $weekDay) {
            if (in_array($dateFrom->dayOfWeekIso, $weekDay['weekdays'])) {
                $priceCalculationByTime[] = [
                    'date' => $dateFrom->format('Y-m-d H:i:s') . ' - ' . $dateTo->format('Y-m-d H:i:s'),
                    'price' => (float)$weekDay['price'],
                    'description' => "Фиксированная цена для " . $dateFrom->locale('ru')->dayName
                ];
                return true;
            }
        }
        return false;
    }

    private static function calculatePriceByGuests(array $rules, int $guests): array
    {
        $guestLimit = (int)$rules['guest_limit']['count'];
        $extraGuestFee = (float)$rules['guest_limit']['extra_guest_fee'];
        $currentGuests = $guests;

        if ($currentGuests > $guestLimit) {
            return [
                'guests' => $currentGuests,
                'price' => ($currentGuests - $guestLimit) * $extraGuestFee,
                'description' => "Дополнительные гости"
            ];
        }
        return [];
    }

    private static function calculateTotalPriceByTimeAndGuests(array $priceDetails): float
    {
        $totalPriceByTime = array_sum(array_column($priceDetails['by_time'], 'price'));
        $totalPriceByGuests = $priceDetails['by_guests']['price'] ?? 0;
        return $totalPriceByTime + $totalPriceByGuests;
    }

    /**
     * @throws PlanNotFound
     */
    private static function calculateDiscountByTimeAndGuests(OrderableServiceObject $serviceObject, Carbon $startTime, Customer $customer, array $priceDetails, ?Coupon $coupon): float
    {
        $totalPriceByTime = array_sum(array_column($priceDetails['by_time'], 'price'));
        return $totalPriceByTime * self::getDiscountPercent($serviceObject, $startTime, $customer, $coupon) / 100;
    }

    /**
     * @throws PlanNotFound
     */
    private static function getDiscountPercent(OrderableServiceObject $serviceObject, Carbon $startTime, Customer $customer, ?Coupon $coupon): float
    {
        // Проверка, является ли текущий план фиксированным
        $plan = self::getCurrentPlanOrFail($serviceObject, $startTime);
        if ($plan->plan_type == PlanType::FIXED) {
            return 0.0;  // Никакая скидка не применяется, если план фиксированный
        }
        $discountData = LoyaltyProgramService::calculateDiscountPercentOnDate($customer, $startTime, $serviceObject->getObjectTimezone(), $coupon);
        return $discountData['discount'];
    }
}
