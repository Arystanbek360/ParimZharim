<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;


use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Modules\Shared\Payment\Domain\Services\PaymentService;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\Payment;

class CancelPayment extends BaseAction {
    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ){}


    public function handle(Payment $payment): void {
        $paymentMethodType = $payment->payment_method;
        $paymentServiceClass = GetPaymentServiceByPaymentMethod::make()->handle($paymentMethodType);

        /** @var PaymentService $paymentService */
        $paymentService = new $paymentServiceClass($this->paymentRepository);
        $paymentService->cancel($payment);
    }


}
