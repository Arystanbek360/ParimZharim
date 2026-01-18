<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Schedule;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Outl1ne\MultiselectField\Multiselect;
use Stepanenko3\NovaJson\Fields\JsonRepeatable;

class ScheduleAdminResource extends BaseAdminResource {
    /**
     * The model the resource corresponds to.
     */
    public static $model = Schedule::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'name';

    public static $search = [
        'id', 'name'
    ];

    public static function label(): string
    {
        return 'Расписание';
    }


    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            JsonRepeatable::make('Правила для дней недели', 'metadata->rules->week_days')
                ->rules([
                    'array',
                ])
                ->fields($this->weekdayFields()),

            JsonRepeatable::make('Правила для конкретных дней', 'metadata->rules->concrete_days')
                ->rules([
                    'array'
                ])
                ->fields($this->concreteDayFields()),
        ];
    }

    private function weekdayFields(): array
    {
        return [
            Multiselect::make('День недели', 'weekdays')
                ->options(
                    [
                        ['value' => '1', 'label' => 'Понедельник'],
                        ['value' => '2', 'label' => 'Вторник'],
                        ['value' => '3', 'label' => 'Среда'],
                        ['value' => '4', 'label' => 'Четверг'],
                        ['value' => '5', 'label' => 'Пятница'],
                        ['value' => '6', 'label' => 'Суббота'],
                        ['value' => '7', 'label' => 'Воскресенье'],
                    ]
                )
                ->saveAsJSON()
                ->placeholder('Выберите дни'),

            Text::make('Время с', 'time_from')
                ->rules('required', 'date_format:H:i')
                ->resolveUsing(function ($value) {
                    if ($value) {
                        $dt = DateTime::createFromFormat('H:i:s', $value) ?: DateTime::createFromFormat('H:i', $value);
                        return $dt ? $dt->format('H:i') : null;
                    }
                    return null;
                })
                ->help('Введите время в формате ЧЧ:мм'),

            Text::make('Время до', 'time_to')
                ->rules('required', 'date_format:H:i')
                ->resolveUsing(function ($value) {
                    if ($value) {
                        $dt = DateTime::createFromFormat('H:i:s', $value) ?: DateTime::createFromFormat('H:i', $value);
                        return $dt ? $dt->format('H:i') : null;
                    }
                    return null;
                })
                ->help('Введите время в формате ЧЧ:мм'),

            Number::make('Мин. время, мин', 'min_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),

            Number::make('Макс. время, мин', 'max_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),

            Number::make('Время обсл., мин', 'service_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),

            Number::make('Ожидание подтв., мин', 'confirmation_waiting_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),
        ];
    }

    private function concreteDayFields(): array
    {
        return [
            Multiselect::make('День', 'days')
                ->options(
                // all days of current and next year
                    collect(range(0, 730))
                        ->map(function ($day) {
                            return [
                                'value' => date('d-m-Y', strtotime("+$day days")),
                                'label' => date('d.m.Y', strtotime("+$day days"))
                            ];
                        })
                        ->values()
                )
                ->saveAsJSON()
                ->placeholder('Выберите дни'),

            Text::make('Время с', 'time_from')
                ->rules('required', 'date_format:H:i')
                ->resolveUsing(function ($value) {
                    if ($value) {
                        $dt = DateTime::createFromFormat('H:i:s', $value) ?: DateTime::createFromFormat('H:i', $value);
                        return $dt ? $dt->format('H:i') : null;
                    }
                    return null;
                })
                ->help('Введите время в формате ЧЧ:мм'),

            Text::make('Время до', 'time_to')
                ->rules('required', 'date_format:H:i')
                ->resolveUsing(function ($value) {
                    if ($value) {
                        $dt = DateTime::createFromFormat('H:i:s', $value) ?: DateTime::createFromFormat('H:i', $value);
                        return $dt ? $dt->format('H:i') : null;
                    }
                    return null;
                })
                ->help('Введите время в формате ЧЧ:мм'),

            Number::make('Мин. время, мин', 'min_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),

            Number::make('Макс. время, мин', 'max_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),

            Number::make('Время обсл., мин', 'service_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),

            Number::make('Ожидание подтв., мин', 'confirmation_waiting_duration')
                ->sortable()
                ->rules('required', 'integer', 'min:0'),
        ];
    }
}
