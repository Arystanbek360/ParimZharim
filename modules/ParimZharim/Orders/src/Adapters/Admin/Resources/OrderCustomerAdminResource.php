<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\LoyaltyProgram\Adapters\Admin\Resources\LoyaltyProgramCustomerAdminResource;
use Modules\ParimZharim\Orders\Domain\Models\OrderCustomer;
use Modules\ParimZharim\Profile\Adapters\Admin\Resources\CustomerAdminResource;

class OrderCustomerAdminResource extends CustomerAdminResource {
    public static string $model = OrderCustomer::class;

    public function fields(NovaRequest $request): array
    {
       return [
           ...parent::fields($request),
           HasMany::make('Заказы', 'orders', OrderAdminResource::class)
           ->sortable(),
       ];
    }
}
