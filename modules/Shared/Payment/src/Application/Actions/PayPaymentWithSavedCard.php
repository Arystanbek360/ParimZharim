<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use RuntimeException;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentCardRepository;
use Modules\Shared\Payment\Infrastructure\Services\TipTopPayPaymentService;

class PayPaymentWithSavedCard extends BaseAction
{
    public function __construct(
        private readonly PaymentCardRepository $paymentCardRepository,
        private readonly TipTopPayPaymentService $paymentService,
    ) {}

    public function handle(Payment $payment, int $paymentCardId): Payment
    {
        if ($payment->status !== PaymentStatus::CREATED) {
            throw new RuntimeException('Payment is not available for saved card payment.');
        }

        $card = $this->paymentCardRepository->getActiveCardForCustomer($paymentCardId, $payment->customer_id);
        if (!$card) {
            throw new RuntimeException('Saved card was not found.');
        }

        $payment = $this->paymentService->payByToken($payment, $card);

        if ($payment->status === PaymentStatus::SUCCESS) {
            CompletePayment::make()->handle($payment);
            $payment->refresh();
        }

        return $payment;
    }
}
