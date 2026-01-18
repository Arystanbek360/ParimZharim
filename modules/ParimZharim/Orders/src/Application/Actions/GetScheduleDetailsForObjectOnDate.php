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
            ->get();

        $minDuration = $schedules
            ->map(function ($schedule) use ($dayOfWeek) {
                $weekDaysData = collect($schedule->metadata['rules']['week_days']);
                $relevantData = $weekDaysData->first(function ($weekDay) use ($dayOfWeek) {
                    return in_array($dayOfWeek, $weekDay['weekdays']);
                });

                return $relevantData ? $relevantData['min_duration'] : null;
            })
            ->filter()
            ->min();

        $confirmationWaitingDuration = $schedules
            ->map(function ($schedule) use ($dayOfWeek) {
                $weekDaysData = collect($schedule->metadata['rules']['week_days']);
                $relevantData = $weekDaysData->first(function ($weekDay) use ($dayOfWeek) {
                    return in_array($dayOfWeek, $weekDay['weekdays']);
                });

                return $relevantData ? (int)$relevantData['confirmation_waiting_duration'] : null;
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
