<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Errors\WrongPaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Services\CloudPaymentServiceInterface;
use Modules\Shared\Payment\Infrastructure\Services\CashPaymentService;
use Modules\Shared\Payment\Infrastructure\Services\KaspiPaymentService;

class GetPaymentServiceByPaymentMethod extends BaseAction {

    /**
     * @throws WrongPaymentMethodType
     */
    public function handle(PaymentMethodType $paymentMethodType): string
    {
        return match ($paymentMethodType) {
            PaymentMethodType::CASH => CashPaymentService::class,
            PaymentMethodType::KASPI => KaspiPaymentService::class,
            PaymentMethodType::CLOUD_PAYMENT => app(CloudPaymentServiceInterface::class)::class,
            PaymentMethodType::ApplePay => app(CloudPaymentServiceInterface::class)::class,
            default => throw new WrongPaymentMethodType('Неизвестный тип оплаты')
        };
    }
}
