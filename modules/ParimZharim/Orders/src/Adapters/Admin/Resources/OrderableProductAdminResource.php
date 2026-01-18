<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableProduct;
use Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources\ProductAdminResource;

class OrderableProductAdminResource extends ProductAdminResource {

    public static string $model = OrderableProduct::class;

    public function fields(NovaRequest $request): array
    {
        return [...parent::fields($request)];
    }
}
