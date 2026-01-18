<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Devcraft\DatetimeWoTimezone\DatetimeWoTimezone;
use DigitalCreative\MegaFilter\MegaFilter;
use DigitalCreative\MegaFilter\MegaFilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\ApplyCouponForOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\CancelOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\CompleteOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\ConfirmOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\FinishOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\RecalculateOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\SyncOrderWithExternalSystemAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderCreatorFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderObjectCategoryFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderDateFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderSourceFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderStatusFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\ActiveOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\MobileAppOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\OrdersToSyncInExternalSystemAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\WaitCancellationOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\WaitConfirmationOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\WaitServiceOrdersAdminResource;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentAdminResource;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Throwable;

class OrderAdminResource extends BaseAdminResource
{

    use MegaFilterTrait;

    public static string $model = Order::class;

    public static $title = 'id';

    public static $search = [
        'id', 'customer.name', 'customer.phone', 'metadata->notes', 'metadata->customerNotes'
    ];
    public static array $indexDefaultOrder = [
        'start_time' => 'desc'
    ];

    //add label
    public static function label(): string
    {
        return 'Заказ';
    }

    public function fields(NovaRequest $request): array
    {
        Carbon::setLocale('ru');
        $isDateStartReadonly = $this->resource && $this->resource->status === OrderStatus::STARTED;

        return [
            new Panel('Информация о заказе', [
                ID::make()->sortable(),

                Text::make('Категория', 'category_name')
                    ->hideWhenCreating()
                    ->hideWhenUpdating()
                    ->displayUsing(function () {
                        // Accessing the orderableServiceObject relationship and then the category relationship
                        return $this->orderableServiceObject->category->name ?? 'No Category';
                    }),

                BelongsTo::make('Объект', 'orderableServiceObject', OrderableServiceObjectAdminResource::class)
                    ->sortable()
                    ->rules('required')
                    ->withMeta([
                        'value' => $request->viaResource == 'orderable-service-object-admin-resources'
                            ? $request->viaResourceId
                            : ($this->orderableServiceObject ? $this->orderableServiceObject->name : null)
                    ])
                    // ->withMeta(['value' => $request->viaResourceId ?? $this->orderableServiceObject->name ?? ''])
                    ->display(function ($orderableServiceObject) {
                        return $orderableServiceObject ? $orderableServiceObject->name : '';
                    })
                    ->readonly(!is_null($request->resourceId))
                    ->help(!is_null($request->resourceId) ? 'Для изменения объекта заказа необходимо отменить текущий заказ и создать новый.' : ''),
                BelongsTo::make('Клиент', 'customer', OrderCustomerAdminResource::class)
                    ->sortable()
                    ->rules('required')
                    ->showCreateRelationButton()
                    //display customer name and customer phone
                    ->display(function ($customer) {
                        return $customer->name . ' ' . $customer->phone;
                    })
                    ->searchable(),
                Number::make('Взрослые', 'metadata->guests_adults')
                    ->sortable()
                    ->hideFromIndex()
                    ->rules('required', 'integer', 'min:0'),
                Number::make('Дети старше 7 лет', 'metadata->guests_children')
                    ->sortable()
                    ->hideFromIndex()
                    ->rules('nullable', 'integer', 'min:0'),
                DatetimeWoTimezone::make('Время начала', 'start_time')
                    ->displayUsing(function ($value) {
                        $convertedValue = $this->convertUtcToOrderTimezone($value);
                        return $convertedValue ? $convertedValue->isoFormat('dd, D MMMM YYYY HH:mm') : 'Нет данных';
                    })
                    ->resolveUsing(function ($value) {
                        return $this->convertUtcToOrderTimezone($value, true);
                    })
                    ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = $this->convertOrderTimezoneToUtc($request->input($requestAttribute));
                    })
                    ->readonly($isDateStartReadonly)
                    ->step(CarbonInterval::minutes(30))
                    ->rules('required', 'date')
                    ->sortable(),
                DatetimeWoTimezone::make('Время окончания', 'end_time')
                    ->dependsOn(['start_time'], function (DatetimeWoTimezone $field, NovaRequest $request, FormData $formData) {
                        $start_time = $formData->start_time;
                        if (!$start_time) {
                            return;
                        }

                        // Check if this is a create request
                        if (!$request->resourceId) {
                            // If end_time is empty, add 3 hours to start_time and set it as the value for end_time
                            $field->setValue(Carbon::parse($start_time)->addHours(3));
                        }
                    })
                    ->displayUsing(function ($value) {
                        $convertedValue = $this->convertUtcToOrderTimezone($value);
                        return $convertedValue ? $convertedValue->isoFormat('dd, D MMMM YYYY HH:mm') : 'Нет данных';
                    })
                    ->resolveUsing(function ($value) {
                        return $this->convertUtcToOrderTimezone($value, true);
                    })
                    ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = $this->convertOrderTimezoneToUtc($request->input($requestAttribute));
                    })
                    ->step(CarbonInterval::minutes(30))
                    ->rules('required', 'date')
                    ->sortable(),
                Textarea::make('Примечания', 'metadata->notes')
                    ->sortable()
                    ->alwaysShow(),
                Textarea::make('Примечания клиента', 'metadata->customerNotes')
                    ->sortable()
                    ->alwaysShow()
                    ->help('Отображаются клиенту в мобильном приложении'),
                Select::make('Источник заказа', 'metadata->source')
                    ->options(OrderSource::labels())
                    ->displayUsingLabels()
                    ->sortable()
                    ->exceptOnForms(),
                Select::make('Статус', 'status')
                    ->options(OrderStatus::labels())
                    ->displayUsingLabels()
                    ->sortable()
                    ->exceptOnForms(),
                DatetimeWoTimezone::make('Дата создания', 'created_at')
                    ->displayUsing(function ($value) {
                        return $this->convertUtcToOrderTimezone($value)->isoFormat('dd,  D MMMM YYYY HH:mm');
                    })
                    ->hideWhenCreating()
                    ->hideWhenUpdating()
                    ->hideFromIndex(),
                Hidden::make('Is Synced In External System', 'metadata->is_synced_in_external_system')
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->hideWhenUpdating()
                    ->default(false),
            ]),

            new Panel('Платежная информация', [
                Select::make('Способ оплаты', 'paymentMethod')
                    ->options(PaymentMethodType::labels())
                    ->displayUsingLabels()
                    ->rules('required')
                    ->hideWhenCreating()
                    ->hideWhenUpdating(),
                Currency::make('Стоимость бронирования', 'serviceObjectOrderItemsTotal')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->currency('KZT')
                    ->sortable()
                    ->rules('required', 'numeric', 'min:0'),
                Text::make('Скидка', 'customerDiscount')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->sortable()
                    ->displayUsing(function ($value) {
                        return $value . '%';
                    }),
                Currency::make('Стоимость бронирования со скидкой', 'serviceObjectOrderItemsTotalWithDiscount')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->currency('KZT')
                    ->sortable()
                    ->rules('required', 'numeric', 'min:0'),
                Currency::make('Стоимость меню', 'productOrderItemsTotal')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->currency('KZT')
                    ->sortable()
                    ->rules('required', 'numeric', 'min:0'),
                Currency::make('Стоимость услуг', 'serviceOrderItemsTotal')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->currency('KZT')
                    ->sortable()
                    ->rules('required', 'numeric', 'min:0'),
                Currency::make('Итого', 'totalWithDiscount')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->currency('KZT')
                    ->sortable()
                    ->rules('required', 'numeric', 'min:0'),
                Currency::make('Ожидаемая предоплата', 'metadata->expectedAdvancePayment')
                    ->resolveUsing(function ($value) {
                        return $value ?? 0;
                    })
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->currency('KZT')
                    ->sortable()
                    ->readonly()
                    ->rules('nullable', 'numeric', 'min:0'),
                Currency::make('Фактическая предоплата', 'actualAdvancePayment')
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->currency('KZT')
                    ->sortable()
                    ->readonly()
                    ->rules(
                        'nullable',
                        'numeric',
                        'min:0',
                        'gte:metadata->expectedAdvancePayment'
                    ),
                Currency::make('Итого к оплате', 'totalToPay')
                    ->exceptOnForms()
                    ->hideFromIndex()
                    ->currency('KZT')
                    ->sortable()
                    ->rules('required', 'numeric', 'min:0'),

            ]),

            HasMany::make('Заказываемый объект', 'serviceObjectOrderItems', OrderableServiceObjectOrderItemAdminResource::class)
                ->readonly(),
            HasMany::make('Заказанные услуги', 'serviceOrderItems', OrderItemServiceAdminResource::class),
            HasMany::make('Заказанные позиции меню', 'productOrderItems', OrderItemProductAdminResource::class),

            HasMany::make('Платежи', 'payments', PaymentAdminResource::class),
        ];

    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new ConfirmOrderAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('confirmOrder', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('confirmOrder', $resource);
                })
                ->showInline()
                ->onlyOnDetail()
                ->onlyInline()
                ->confirmText('Заказ будет подтвержден только после подтверждения предоплаты.
                 Вы уверены, что хотите внести предоплату по заказу?')
                ->confirmButtonText('Внести предоплату по заказу'),

            (new CancelOrderAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('cancelOrder', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('cancelOrder', $resource);
                })
                ->showInline()
                ->confirmText('Вы уверены, что хотите отменить этот заказ?')
                ->confirmButtonText('Отменить заказ'),
            (new FinishOrderAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('finishOrder', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('finishOrder', $resource);
                })
                ->confirmText('Вы уверены, что хотите досрочно завершить этот заказ?')
                ->confirmButtonText('Завершить заказ'),
            (new CompleteOrderAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('completeOrder', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('completeOrder', $resource);
                })
                ->confirmText('Вы уверены, что хотите подтвердить выполнение заказа?')
                ->confirmButtonText('Подтвердить выполнение заказа'),
            (new SyncOrderWithExternalSystemAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('syncOrderWithExternalSystem', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('syncOrderWithExternalSystem', $resource);
                })
                ->confirmText('Вы действительно внесли данные в IIKO?')
                ->confirmButtonText('Синхронизировать заказ'),
            (new ApplyCouponForOrderAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('applyDiscount', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                   return true;
                })
                ->onlyOnDetail()
                ->onlyInline()
                ->confirmText('Вы уверены, что хотите применить купон к заказу?')
                ->confirmButtonText('Применить купон'),

            (new RecalculateOrderAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('recalculateOrder', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return true;
                })
                ->showInline()
                ->confirmText('Вы уверены, что хотите пересчитать заказ?')
                ->confirmButtonText('Пересчитать заказ'),

            (new DownloadExcel)->withHeadings([
                'ID',
                'Категория',
                'Объект',
                'Клиент',
                'Взрослые',
                'Дети старше 7 лет',
                'Время начала',
                'Время окончания',
                'Примечания',
                'Примечания клиента',
                'Статус',
                'Синхронизирован в iiko',
                'Стоимость бронирования',
                'Скидка',
                'Стоимость бронирования со скидкой',
                'Стоимость меню',
                'Стоимость услуг',
                'Итого',
                'Ожидаемая предоплата',
                'Фактическая предоплата',
                'Итого к оплате',
            ])
                ->withoutConfirmation()
                ->only([
                    'id',
                    'categoryName',
                    'orderableServiceObject',
                    'customer',
                    'guestsAdults',
                    'guestsChildren',
                    'orderStartTime',
                    'orderEndTime',
                    'notes',
                    'customerNotes',
                    'orderStatus',
                    'isSyncedInExternalSystem',
                    'serviceObjectOrderItemsTotal',
                    'customerDiscount',
                    'serviceObjectOrderItemsTotalWithDiscount',
                    'productOrderItemsTotal',
                    'serviceOrderItemsTotal',
                    'totalWithDiscount',
                    'expectedAdvancePayment',
                    'actualAdvancePayment',
                    'totalToPay'
                    ])
                ->withFilename('Orders_' . $this->convertUtcToOrderTimezone(Carbon::now())->format('Y-m-d_H-i-s') . '.xlsx')
                ->withDisk('local')
            ->withName('Экспорт в Excel')
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('viewAny', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('viewAny', $resource);
                })
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new ActiveOrdersAdminResource(),
            new WaitConfirmationOrdersAdminResource(),
            new WaitCancellationOrdersAdminResource(),
            new WaitServiceOrdersAdminResource(),
            new OrdersToSyncInExternalSystemAdminResource(),
            new MobileAppOrdersAdminResource(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        //add name
        return [
            MegaFilter::make([
                new OrderObjectCategoryFilter(),
                new OrderStatusFilter(),
                new OrderDateFilter(),
                new OrderCreatorFilter(),
                new OrderSourceFilter(),
            ])->columns(5)->open(),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder));
        }
        return $query->with(['customer', 'creator', 'orderableServiceObject', 'orderItems']);
    }

    public static function detailQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(['customer', 'creator', 'orderableServiceObject', 'orderItems']);
    }

    private function getOrderTimezone(): string
    {
        // Replace with your logic to retrieve the appropriate time zone for the order
        // For example, assume that the `timezone` field is present in the `Order` model.
        $order = $this->resource;
        $object = $order->orderableServiceObject;
        if (!$object) {
            return 'Asia/Almaty';
        }
        return $object->getObjectTimezone();
    }

    private function convertUtcToOrderTimezone(?Carbon $value, bool $forEditing = false): ?Carbon
    {
        $timezone = $this->getOrderTimezone();

        if ($value) {
            try {
                // Если преобразование для экрана редактирования, корректируем смещение
                if ($forEditing) {
                    // Определяем смещение в минутах и добавляем к времени
                    $offset = Carbon::now($timezone)->utcOffset();
                    return $value->addMinutes($offset);
                } else {
                    // Иначе просто переводим в нужный часовой пояс
                    return $value->setTimezone($timezone);
                }
            } catch (Throwable $e) {
                throw $e;
            }
        }

        return null;
    }

    //TODO: shift timezone
    private function convertOrderTimezoneToUtc(string $inputDate): ?Carbon
    {
        $timezone = $this->getOrderTimezone();

        try {
            // Сначала парсим дату как UTC
            $localTime = Carbon::parse($inputDate, 'UTC');

            // Получаем смещение между нужным часовым поясом и UTC (в секундах)
            $targetOffset = Carbon::now($timezone)->getOffset();

            // Вычитаем это смещение, чтобы откорректировать время в UTC
            return $localTime->subSeconds($targetOffset)->setTimezone('UTC');
        } catch (\Throwable $e) {
            // Логируем ошибку для дальнейшего анализа
            throw $e;
        }
    }

}
