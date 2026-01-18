<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses;

use Illuminate\Support\Carbon;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Nova;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Actions\MarkFailedPaymentShownAdminAction;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentItemAdminResource;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Pavloniym\ActionButtons\ActionButton;
use Pavloniym\ActionButtons\ActionButtons;

class FailedOrderPaymentAdminResource extends Lens
{

    public static $polling = true;

    public static $pollingInterval = 30;

    public static function query(LensRequest $request, $query)
    {
        $query = $query->whereIn('status', [
            PaymentStatus::FAILED
        ])->where(function ($query) {
            $query->whereNull('metadata->is_marked_as_shown')
                ->orWhere('metadata->is_marked_as_shown', false);
        });

        $query = $request->withFilters($query);

        return $request->withOrdering($query);
    }

    public function fields(NovaRequest $request)
    {
        Carbon::setLocale('ru');
        $fields = [
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

        return $fields;
    }

    private function convertUtcToTimezone(?Carbon $value): ?Carbon
    {
        $timezone = 'Asia/Almaty';
        return $value->setTimezone($timezone);
    }

    public function uriKey(): string
    {
        return 'failed-order-payment-admin-resource';
    }

    public static function label(): string
    {
        return 'Неудачные платежи';
    }

    public function name(): string
    {
        return 'Неудачные платежи';
    }
}
