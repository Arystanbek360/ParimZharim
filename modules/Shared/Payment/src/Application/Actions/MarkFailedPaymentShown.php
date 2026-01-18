<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;

class MarkFailedPaymentShown extends BaseAction
{

    public function __construct(
        private readonly PaymentRepository $paymentRepository
    )
    {}

    public function handle(int $orderID): void
    {
        $payment = QueryPaymentByID::make()->handle($orderID);
        $metadata = is_string($payment->metadata) ? json_decode($payment->metadata, true) : $payment->metadata;
        $metadata['is_marked_as_shown'] = true;
        $payment->metadata = $metadata;
        $this->paymentRepository->savePayment($payment);
    }
}
