<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableService;
use Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources\ServiceAdminResource;

class OrderableServiceAdminResource extends ServiceAdminResource {

    public static string $model = OrderableService::class;

    public function fields(NovaRequest $request): array
    {
        return [
            ...parent::fields($request),
            BelongsToMany::make('Объекты', 'orderableServiceObjects', OrderableServiceObjectAdminResource::class)
            ->sortable()
            ->filterable(),
        ];
    }
}
