<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceOrderItem;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class OrderItemServiceAdminResource extends BaseAdminResource {

    public static string $model = OrderableServiceOrderItem::class;

    public static $perPageViaRelationship = 20;

    public static $title = 'id';

    public static $search = [
        'id'
    ];

    public static function label(): string
    {
        return 'Услуга к заказу';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Заказ', 'order', OrderAdminResource::class)
                ->sortable()
                ->exceptOnForms()
                ->hideFromIndex()
                ->rules('required')
                ->filterable(),

           BelongsTo::make('Услуга', 'orderable', OrderableServiceAdminResource::class)
                ->sortable()
                ->searchable()
                ->rules('required')
                ->filterable(),

            Number::make('Количество', 'quantity')
                ->sortable()
                ->rules('required', 'integer', 'min:1'),

            Currency::make('Цена', 'price')
                ->sortable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->currency('KZT'),

            Currency::make('Стоимость', 'total')
                ->sortable()
                ->hideWhenCreating()
                ->currency('KZT')
                ->hideWhenUpdating(),
        ];
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource): string
    {
        return '/resources/'.OrderAdminResource::uriKey().'/'.$resource->order_id;
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return '/resources/'.OrderAdminResource::uriKey().'/'.$resource->order_id;
    }

}
