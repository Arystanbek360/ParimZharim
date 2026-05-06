<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\Shared\Core\Application\BaseAction;

class GetScheduleDetailsForObjectOnDate extends BaseAction
{

    public function handle(OrderableServiceObject $object, Carbon $date): array
    {
        $dayOfWeek = $date->dayOfWeekIso;

        $schedules = $object->schedules()
            ->wherePivot('date_from', '<=', $date)
            ->orderByPivot('date_from', 'desc')
            ->limit(1)
            ->get();

        $relevantRules = $schedules
            ->flatMap(function ($schedule) use ($date, $dayOfWeek) {
                $rules = $schedule->metadata['rules'] ?? [];
                $day = $date->format('d-m-Y');
                $concreteDayRules = collect($rules['concrete_days'] ?? [])
                    ->filter(function ($rule) use ($day) {
                        return in_array($day, $rule['days'] ?? []);
                    });

                if ($concreteDayRules->isNotEmpty()) {
                    return $concreteDayRules;
                }

                return collect($rules['week_days'] ?? [])
                    ->filter(function ($weekDay) use ($dayOfWeek) {
                        return in_array($dayOfWeek, $weekDay['weekdays'] ?? []);
                    });
            });

        $minDuration = $relevantRules
            ->map(function ($rule) {
                return $rule['min_duration'] ?? null;
            })
            ->filter()
            ->min();

        $confirmationWaitingDuration = $relevantRules
            ->map(function ($rule) {
                return isset($rule['confirmation_waiting_duration']) ? (int)$rule['confirmation_waiting_duration'] : null;
            })
            ->filter()
            ->min();


        $confirmationWaitingDuration = $confirmationWaitingDuration ?? null;
        $minDuration = $minDuration ?? null;

        return [
            'confirmation_waiting_duration' => $confirmationWaitingDuration,
            'min_duration' => $minDuration,
        ];
    }

}
