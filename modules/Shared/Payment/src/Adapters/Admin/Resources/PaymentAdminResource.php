<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Admin\Resources;

use Illuminate\Support\Carbon;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Actions\CancelPaymentAdminAction;
use Modules\Shared\Payment\Adapters\Admin\Actions\CompletePaymentAdminAction;
use Modules\Shared\Payment\Adapters\Admin\Actions\MarkFailedPaymentShownAdminAction;
use Modules\Shared\Payment\Application\Actions\MarkFailedPaymentShown;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Pavloniym\ActionButtons\ActionButton;
use Pavloniym\ActionButtons\ActionButtons;

class PaymentAdminResource extends BaseAdminResource
{

    public static string $model = Payment::class;

    public static $title = 'id';

    public static $search = [
        'id', 'total'
    ];

    public static function label(): string
    {
        return 'Платежи к заказам';
    }

    public function fields(NovaRequest $request)
    {
        Carbon::setLocale('ru');
        $fields =  [
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

    public function actions(NovaRequest $request): array
    {
        return [
            (new CancelPaymentAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('cancelPayment', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('cancelPayment', $resource);
                })
                ->showInline()
                ->confirmText('Вы уверены, что хотите отменить платеж?')
                ->confirmButtonText('Отменить'),

            (new CompletePaymentAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('completePayment', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('completePayment', $resource);
                })
                ->showInline()
                ->confirmText('Вы уверены, что хотите завершить платеж?')
                ->confirmButtonText('Завершить'),

            (new MarkFailedPaymentShownAdminAction())
            ->canSee(function (NovaRequest $request) {
                if ($request instanceof ActionRequest) {
                    return true;
                }
                return $request->user()->can('markFailedPaymentShown', $this->resource);
            })
            ->canRun(function (NovaRequest $request, $resource) {
                return $request->user()->can('markFailedPaymentShown', $resource);
            })
            ->showInline()
            ->confirmText('Вы уверены, что хотите пометить платеж как просмотренный?')
            ->confirmButtonText('Пометить просмотренным'),
        ];
    }

}
