<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Plan;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\PlanType;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Outl1ne\MultiselectField\Multiselect;
use Stepanenko3\NovaJson\Fields\JsonRepeatable;

class PlanAdminResource extends BaseAdminResource {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Plan::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
    ];

    public static function label(): string
    {
        return 'Тариф';
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Number::make('Базовый лимит гостей', 'metadata->rules->guest_limit->count')
                ->min(0)
                ->step(1)
                ->sortable()
                ->rules('required', 'numeric'),

            Number::make('Стоимость за доп. гостя', 'metadata->rules->guest_limit->extra_guest_fee')
                ->min(0)
                ->step(1)
                ->sortable()
                ->rules('required', 'numeric'),

            Select::make('Тип Оплаты', 'plan_type')
                ->options(PlanType::labels())
                ->displayUsingLabels()
                ->sortable()
                ->rules('required'),

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

            JsonRepeatable::make('Отображение цены в мобильном приложении', 'metadata->mobile_app_price')
                ->rules([
                    'array'
                ])
                ->rules('required')
                ->fields($this->mobileAppFields()),

            Number::make('Депозит по кухне', 'metadata->kitchen_deposit')
                ->min(0)
                ->step(1)
                ->sortable()
                ->rules('nullable', 'numeric'),
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

            Number::make('Цена', 'price')
                ->min(0)
                ->step(1)
                ->sortable()
                ->rules('required', 'numeric')
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

            Number::make('Цена', 'price')
                ->min(0)
                ->step(1)
                ->sortable()
                ->rules('required', 'numeric')
        ];
    }

    private function mobileAppFields(): array
    {
        return [
            Text::make('Название', 'name')
                ->rules('required', 'max:255'),

            Text::make('Цена', 'price')
                ->rules('required')
                ->help('Введите цену в формате "XXX тг/час" или "XXX тг"')
        ];
    }
}
