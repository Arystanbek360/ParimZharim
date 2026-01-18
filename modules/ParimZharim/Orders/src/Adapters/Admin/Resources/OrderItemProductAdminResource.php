<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableProductOrderItem;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class OrderItemProductAdminResource extends BaseAdminResource {

    public static string $model = OrderableProductOrderItem::class;

    public static $perPageViaRelationship = 20;

    public static $title = 'id';

    public static $search = [
        'id'
    ];

    public static function label(): string
    {
        return 'Позиция меню к заказу';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Заказ', 'order', OrderAdminResource::class)
                ->sortable()
                ->exceptOnForms()
                ->rules('required')
                ->filterable(),
            // Use a conditional to add the BelongsTo field only if the orderable relation exists
            $this->when($this->orderable(), function () {
                return BelongsTo::make('Позиция меню', 'orderable', OrderableProductAdminResource::class)
                    ->sortable()
                    ->searchable()
                    ->rules('required')
                    ->filterable();
            }),

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
                ->hideWhenUpdating()
                ->currency('KZT'),

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
