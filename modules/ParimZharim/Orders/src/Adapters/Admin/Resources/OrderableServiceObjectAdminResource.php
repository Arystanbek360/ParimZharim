<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Devcraft\DatetimeWoTimezone\DatetimeWoTimezone;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Modules\ParimZharim\Objects\Adapters\Admin\Resources\CategoryAdminResource;
use Modules\ParimZharim\Objects\Adapters\Admin\Resources\ServiceObjectAdminResource;
use Modules\ParimZharim\Objects\Adapters\Admin\Resources\TagAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\CreateOrderForServiceObjectAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\SetTechnicalReserveForOrderableServiceObjectAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\OrderableObjectSlotsTableAdminResource;
use Modules\ParimZharim\Orders\Application\Actions\GetMergedFreeSlotsForServiceObjectOnDate;
use Modules\ParimZharim\Orders\Application\Actions\GetSlotsForServiceObjectOnDate;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;

class OrderableServiceObjectAdminResource extends ServiceObjectAdminResource
{

    public static string $model = OrderableServiceObject::class;

    public static $title = 'name';

    public function fields(NovaRequest $request): array
    {
        Carbon::setLocale('ru');
        return [
            ID::make(),
            Text::make('Наименование Объекта', 'name')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            Panel::make('Информация о занятости Объекта', [
                Text::make('Ближайшие свободные слоты')
                    ->onlyOnDetail()
                    ->asHtml()  // Включаем интерпретацию значения как HTML
                    ->displayUsing(function ($value) {
                        $slots = GetMergedFreeSlotsForServiceObjectOnDate::make()->handle($this->id, now(), now()->addHours(48));

                        return collect($slots)->map(function ($slot) {
                            $startTime = Carbon::parse($slot['start']);
                            $endTime = Carbon::parse($slot['end']);

                            // Форматирование времени для отображения
                            $formattedStart = $startTime->isoFormat('dd, D MMMM YYYY  HH:mm');
                            $formattedEnd = $endTime->format('H:i');

                            // Проверка, начинается ли слот на следующий день
                            if (!$endTime->isSameDay($startTime)) {
                                $dayName = $endTime->format('l'); // Получаем название следующего дня
                                $formattedEnd = $endTime->isoFormat('dd, D MMMM YYYY  HH:mm');
                            }

                            // Поскольку это свободные слоты, статус всегда будет "Свободно"
                            $status = '<span style="color: green;">Свободно</span>';

                            return "{$formattedStart} - {$formattedEnd}: {$status}";
                        })->implode('<br>'); // Используем <br> для перевода строк в HTML
                    }),
                Text::make('Расписание на ближайшее время')
                    ->onlyOnDetail()
                    ->asHtml()  // Включаем интерпретацию значения как HTML
                    ->displayUsing(function ($value) {
                        $slots = GetSlotsForServiceObjectOnDate::make()->handle($this->id, now(), now()->addHours(52));
                        return collect($slots)->take(48 * 2)->map(function ($slot) {
                            //if start time is less than now, skip it
                            $startTime = Carbon::parse($slot['start']);
                            $endTime = Carbon::parse($slot['end']);

                            // Поскольку это расписание на ближайшее время, пропускаем прошедшие слоты
                            if ($startTime->copy()->shiftTimezone($this->resource->getObjectTimezone())->isPast()) {
                                return '';
                            }

                            $formattedStart = $startTime->isoFormat('dd, D MMMM YYYY  HH:mm');
                            $formattedEnd = $endTime->format('H:i');

                            // Визуальное отображение статуса слота
                            $freeStatus = $slot['is_free']
                                ? '<span style="color: green;">Свободно,</span>'
                                : '<span style="color: red;">Занято,</span>';

                            if (!$slot['is_free'] && isset($slot['order_id'])) {
                                $orderLink = '<a target="blank" style="color: blue; text-decoration: underline" href="/nova/resources/order-admin-resources/' . $slot['order_id'] . '">Заказ #' . $slot['order_id'] . '</a>';
                                $orderLink .= ' (' . $slot['is_free_reason'] . ')';
                            } else {
                                $orderLink = $slot['is_free_reason'] ?? '';
                            }

                            $canStartBookingStatus = $slot['can_start_booking']
                                ? '<span style="color: green;">можно начать бронирование</span>'
                                : '<span style="color: red;">нельзя начать бронирование:</span> ' . ($slot['can_start_booking_reason'] ?? '');

                            $reason = $slot['is_free'] ? $canStartBookingStatus : $orderLink;

                            return "$formattedStart - $formattedEnd: $freeStatus $reason";
                        })->implode('<br>'); // Используем <br> для перевода строк в HTML
                    }),
                DatetimeWoTimezone::make('Дата начала технического резерва', 'startTechnicalReserveDateTime')
                    ->rules('required, datetime')
                    ->displayUsing(function ($value) {
                        return $value ? $value->isoFormat('dd, D MMMM YYYY HH:mm') : 'Нет данных';
                    })
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->hideWhenUpdating(),
                DatetimeWoTimezone::make('Дата окончания технического резерва', 'endTechnicalReserveDateTime')
                    ->rules('required, datetime')
                    ->displayUsing(function ($value) {
                        return $value ? $value->isoFormat('dd, D MMMM YYYY HH:mm') : 'Нет данных';
                    })
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->hideWhenUpdating(),
            ])->collapsedByDefault(),
            Panel::make('Информация об объекте', [
                Textarea::make('Описание объекта', 'description')->hideFromIndex()
                    ->rules('required', 'max:255'),
                BelongsTo::make('Категория', 'category', CategoryAdminResource::class)
                    ->showCreateRelationButton(),
                Number::make('Вместимость', 'capacity')->sortable()
                    ->filterable()
                    ->rules('required', 'max:255'),
                //is_active
                Boolean::make('Активен?', 'is_active')->sortable()
                    ->filterable()
                    ->rules('required', 'max:255'),
                Images::make('Главное изображение', 'main') // second parameter is the media collection name
                ->conversionOnIndexView('thumb') // conversion used to display the image
                ->rules('required')
                    ->setFileName(function ($originalFilename, $extension, $model) {
                        return 'main-image-' . uniqid() . '.' . $extension;
                    }),
                Images::make('Галерея', 'gallery')
                    ->hideFromIndex()
                    ->conversionOnDetailView('thumb') // conversion used on the model's view
                    ->conversionOnForm('thumb') // conversion used to display the image on the model's form
                    ->fullSize(),

                Tag::make('Теги', 'tags', TagAdminResource::class)
                    ->showCreateRelationButton(),
            ]),

            BelongsToMany::make('Услуги', 'services', OrderableServiceAdminResource::class),
            BelongsToMany::make('Расписания', 'schedules', ScheduleAdminResource::class)
                ->fields(function () {
                    return [
                        Date::make('Дата начала', 'date_from')
                            ->displayUsing(function ($value) {
                                return Carbon::parse($value)->isoFormat('dd, D MMMM YYYY');
                            }),
                    ];
                })
                ->searchable()
                ->sortable()
                ->filterable(),
            BelongsToMany::make('Тарифы', 'plans', PlanAdminResource::class)
                ->fields(function () {
                    return [
                        Date::make('Дата начала', 'date_from')
                            ->displayUsing(function ($value) {
                                return Carbon::parse($value)->isoFormat('dd, D MMMM YYYY');
                            }),
                    ];
                })
                ->searchable()
                ->sortable()
                ->filterable(),

            HasMany::make('Заказы', 'orders', OrderAdminResource::class)
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new OrderableObjectSlotsTableAdminResource()
        ];
    }

    public function actions(NovaRequest $request): array
    {

        $resourceId = $request->resourceId ?? $request->route('resourceId') ?? $request->query('resourceId');

        // Debugging output
        logger()->debug('Determined resourceId before return: ', ['resourceId' => $resourceId]);
        $resourceClass = $request->resource;

        return [
            (new CreateOrderForServiceObjectAdminAction)
                ->openInNewTab('Создать заказ', function () use ($request, $resourceClass) {
                    $resourceId = $request->resourceId ?? $request->route('resourceId');
                    return Nova::url('/resources/order-admin-resources/new?viaResource=' . urlencode($resourceClass) . '&viaResourceId=' . $resourceId);
                })
                ->canSee(function (NovaRequest $request) {
                    return true;
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return true;
                })
                ->showInline()
                ->confirmText('Вы уверены, что хотите создать заказ для этого объекта?')
                ->confirmButtonText('Перейти к созданию заказа'),

            (new SetTechnicalReserveForOrderableServiceObjectAdminAction)
                ->canSee(function (NovaRequest $request) {
                    return true;
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return true;
                })
                ->showInline()
                ->confirmText('Вы уверены, что хотите установить технический резерв для этого объекта?')
                ->confirmButtonText('Установить технический резерв')

        ];


    }
}
