<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\FailedOrderPaymentAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\PendingOrderPaymentAdminResource;
use Modules\ParimZharim\Orders\Domain\Models\OrderPayment;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentAdminResource;

class OrderPaymentAdminResource extends PaymentAdminResource
{
    public static string $model = OrderPayment::class;

    public function fields(NovaRequest $request): array
    {
        Carbon::setLocale('ru');

        return [
            BelongsTo::make('Заказ', 'order', OrderAdminResource::class)
                ->sortable()
                ->readonly(),
            ...parent::fields($request)
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            ...parent::actions($request),
        ];

    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new FailedOrderPaymentAdminResource(),
            new PendingOrderPaymentAdminResource()
        ];
    }

}
