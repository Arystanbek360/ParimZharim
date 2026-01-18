<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses;

use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentItemAdminResource;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

class PendingOrderPaymentAdminResource extends Lens
{

    public static $polling = true;

    public static $pollingInterval = 30;

    public static function query(LensRequest $request, $query)
    {
        $query = $query->whereIn('status', [
                PaymentStatus::PENDING
            ]);

        $query = $request->withFilters($query);

        return $request->withOrdering($query);
    }

    public function fields(NovaRequest $request)
    {
        Carbon::setLocale('ru');
        return [
            BelongsTo::make('Заказ', 'order', OrderAdminResource::class)
                ->sortable()
                ->readonly(),
            ID::make()->sortable(),
            Currency::make('Сумма платежа', 'total')
                ->currency('KZT'),
            Text::make('Статус', 'status')
                ->displayUsing(function ($status) {
                    return PaymentStatus::from($status)->label();
                })
                ->readonly()
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            Select::make('Платежный метод', 'payment_method')
                ->options(PaymentMethodType::labels())
                ->displayUsingLabels()
                ->hideFromIndex(),
            Text::make('Комментарий', 'comment'),
            Text::make('Создан', 'created_at')
                ->displayUsing(function ($value) {
                    return $this->convertUtcToTimezone($value)->isoFormat('dd,  D MMMM YYYY HH:mm');
                })
                ->hideWhenCreating()
                ->readonly(),
            HasMany::make('Позиции платежа', 'items', PaymentItemAdminResource::class),
        ];
    }

    private function convertUtcToTimezone(?Carbon $value): ?Carbon
    {
        $timezone = 'Asia/Almaty';
        return $value->setTimezone($timezone);
    }
    public function uriKey(): string
    {
        return 'pending-order-payment';
    }

    public static function label(): string
    {
        return 'Платежи, ожидающие подтверждения';
    }

    public function name(): string
    {
        return 'Платежи, ожидающие подтверждения';
    }
}
