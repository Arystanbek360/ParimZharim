<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Application\Actions\CreateAdvancePaymentForOrder;
use Modules\ParimZharim\Orders\Application\ApplicationError\AdvancePaymentIsAlreadyCreated;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\Payment\Application\Actions\GetPaymentsMethodsForAdminPanel;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Throwable;

class ConfirmOrderAdminAction extends BaseAdminAction
{

    /**
     * @throws Throwable
     * @throws AdvancePaymentIsAlreadyCreated
     * @throws StatusChangeViolation
     * @throws OrderNotFound
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            // Retrieve and validate paymentMethodType
            $paymentMethodTypeValue = (string)$fields->get('payment_method');

            // Convert to PaymentMethodType instance if not already an instance
            if (!$paymentMethodTypeValue instanceof PaymentMethodType) {
                $paymentMethodType = PaymentMethodType::from($paymentMethodTypeValue);
            } else {
                $paymentMethodType = $paymentMethodTypeValue;
            }

            $actualAdvancePayment = (int)$fields->get('metadata->actualAdvancePayment');

            // Create advance payment
            CreateAdvancePaymentForOrder::make()->handle($model->id, $actualAdvancePayment, $paymentMethodType);
        }
    }

    public function name(): string
    {
        return 'Внести предоплату по заказу';
    }

    public function fields(NovaRequest $request): array
    {
        // Fetch payment methods available for admin
        $paymentMethods = GetPaymentsMethodsForAdminPanel::make()->handle();

        // Prepare options with type as key and label as value
        $paymentMethodOptions = $paymentMethods->mapWithKeys(function ($paymentMethod) {
            return [$paymentMethod->type->value => $paymentMethod->type->label()];
        })->toArray();

        return [
            Currency::make('Фактическая предоплата', 'metadata->actualAdvancePayment')
                ->currency('KZT')
                ->rules('required', 'numeric'),

            Select::make('Способ оплаты', 'payment_method')
                ->options($paymentMethodOptions)
                ->displayUsingLabels()
                ->rules('required'),
        ];
    }
}
