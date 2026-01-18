<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\Shared\Core\Application\BaseAction;

class GetPlanDetailsForObjectOnDate extends BaseAction
{

    public function handle(OrderableServiceObject $object, Carbon $date): array
    {
        $plans = $object->plans()
            ->wherePivot('date_from', '<=', $date)
            ->get();

        if ($plans->isEmpty()) {
            return [];
        }


        $prices = $plans->map(function ($plan) {
            return $plan->metadata['mobile_app_price'] ?? null;
        });


        $kitchenDeposit = $plans->map(function ($plan) {
            return isset($plan->metadata['kitchen_deposit']) ? (int)$plan->metadata['kitchen_deposit'] : null;
        })->filter()->first();

        $guestLimits = $plans->map(function ($plan) {
            return [
                'guest_limit_count' => $plan->metadata['rules']['guest_limit']['count'],
                'extra_guest_fee' => $plan->metadata['rules']['guest_limit']['extra_guest_fee']
            ];

        })->first();

        return [
            'prices' => $prices,
            'kitchen_deposit' => $kitchenDeposit,
            'guest_limits' => $guestLimits
        ];
    }

}
