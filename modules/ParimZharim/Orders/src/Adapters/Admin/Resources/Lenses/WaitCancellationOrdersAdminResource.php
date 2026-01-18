<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Devcraft\DatetimeWoTimezone\DatetimeWoTimezone;
use DigitalCreative\MegaFilter\MegaFilter;
use DigitalCreative\MegaFilter\MegaFilterTrait;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Modules\ParimZharim\Orders\Adapters\Admin\Actions\CancelOrderAdminAction;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderCreatorFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderDateFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderObjectCategoryFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderableServiceObjectAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderCustomerAdminResource;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Laravel\Nova\Http\Requests\ActionRequest;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\OrderSourceFilter;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Throwable;

class WaitCancellationOrdersAdminResource extends Lens {

    use MegaFilterTrait;


    public static $polling = true;

    public static $pollingInterval = 30;

    public static $indexDefaultOrder = [
        'start_time' => 'asc'
    ];
    public function fields(NovaRequest $request): array
    {
        Carbon::setLocale('ru');
        return [
            ID::make('ID', 'id')->sortable(),

            Text::make('Категория', 'category_name')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->displayUsing(function () {
                    // Accessing the orderableServiceObject relationship and then the category relationship
                    return $this->orderableServiceObject->category->name ?? 'No Category';
                }),

            BelongsTo::make('Заказываемый объект', 'orderableServiceObject', OrderableServiceObjectAdminResource::class)
                ->sortable(),

            BelongsTo::make('Клиент', 'customer', OrderCustomerAdminResource::class)
                ->sortable(),

            DatetimeWoTimezone::make('Время начала', 'start_time')
                ->displayUsing(function ($value) {
                    return $this->convertUtcToOrderTimezone($value)->isoFormat('dd, D MMMM YYYY  HH:mm');
                })
                ->resolveUsing(function ($value) {
                    return $this->convertUtcToOrderTimezoneForEditScreen($value);
                })
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = $this->convertOrderTimezoneToUtc($request->input($requestAttribute));
                })
                ->step(CarbonInterval::hour())
                ->rules('required', 'date'),


            DatetimeWoTimezone::make('Время окончания', 'end_time')
                ->displayUsing(function ($value) {
                    return $this->convertUtcToOrderTimezone($value)->isoFormat('dd, D MMMM YYYY  HH:mm');
                })
                ->resolveUsing(function ($value) {
                    return $this->convertUtcToOrderTimezoneForEditScreen($value);
                })
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = $this->convertOrderTimezoneToUtc($request->input($requestAttribute));
                })
                ->step(CarbonInterval::hour())
                ->rules('required', 'date'),

            Textarea::make('Примечания', 'metadata->notes'),
            Textarea::make('Примечания клиента', 'metadata->customerNotes'),
            Select::make('Источник заказа', 'metadata->source')
                ->options(OrderSource::labels())
                ->displayUsingLabels()
                ->sortable()
                ->exceptOnForms(),

            Currency::make('Стоимость бронирования', 'serviceObjectOrderItemsTotal')
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

            Currency::make('Итого', 'total')
                ->currency('KZT')
                ->sortable(),

            Number::make('Взрослые', 'metadata->guests_adults')
                ->sortable()
            ->hideFromIndex(),

            Number::make('Дети старше 7 лет', 'metadata->guests_children')
                ->sortable()
            ->hideFromIndex(),

            Select::make('Статус', 'status')
                ->options(OrderStatus::labels())
                ->displayUsingLabels(),
            Select::make('Способ оплаты', 'paymentMethod')
                ->options(PaymentMethodType::labels())
                ->displayUsingLabels()
                ->rules('required')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
        ];
    }

    public static function query(LensRequest $request, $query)
    {
        // Apply initial filters to the query
        $query = $query->where('deleted_at', '=', null)
            ->where(function ($query) {
                $query->whereIn('status', [
                    OrderStatus::CANCELLATION_REQUESTED,
                ]);
            });

        // Apply additional filters from the request
        $query = $request->withFilters($query);

        // Check if the request has any ordering applied
        if (empty($request->get('orderBy'))) {
            // Clear any previous orders
            $query->getQuery()->orders = [];

            // Apply default order if no custom order is set
            $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder));
        }

        // Apply any additional ordering from the request
        return $request->withOrdering($query);
    }


    public static function label(): string
    {
        return 'Запрос отмены';
    }


    public function name(): string
    {
        return 'Запрос отмены';
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new CancelOrderAdminAction())
                ->showInline()
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('cancelOrder', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('cancelOrder', $resource);
                })
                ->confirmText('Вы уверены, что хотите отменить этот заказ?')
                ->confirmButtonText('Отменить заказ'),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            MegaFilter::make([
                new OrderObjectCategoryFilter(),
                new OrderCreatorFilter(),
                new OrderDateFilter(),
                new OrderSourceFilter()
            ])->columns(4)->open(),
        ];
    }

    public function uriKey(): string
    {
        return 'wait-cancellation-orders';
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


    private function convertUtcToOrderTimezone(?Carbon $value): ?Carbon
    {
        $timezone = $this->getOrderTimezone();

        if ($value) {
            try {
                // Parse the UTC date and adjust to the local time zone
                return Carbon::parse($value, 'UTC')->setTimezone($timezone);
            } catch (Throwable $e) {
                throw $e;
            }
        }

        return null;
    }

    private function convertUtcToOrderTimezoneForEditScreen(?Carbon $value): ?Carbon
    {
        $timezone = $this->getOrderTimezone();

        if ($value) {
            try {
                // Определим смещение для целевого часового пояса
                $offset = Carbon::now($timezone)->utcOffset();

                // Применим это смещение к значению времени
                return $value->addMinutes($offset);
            } catch (Throwable $e) {
                // Обрабатываем исключение по мере необходимости
                throw $e;
            }
        }

        return null;
    }


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
