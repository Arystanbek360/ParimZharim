<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCard;
use Modules\Shared\Payment\Domain\Repositories\PaymentCardRepository;

class SavePaymentCardFromTipTopPayWebhook extends BaseAction
{
    public function __construct(
        private readonly PaymentCardRepository $paymentCardRepository,
    ) {}

    public function handle(Payment $payment, array $paymentSystemData): ?PaymentCard
    {
        if (empty($paymentSystemData['Token'])) {
            return null;
        }

        return $this->paymentCardRepository->saveFromTipTopPayPayment($payment, $paymentSystemData);
    }
}
