<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;


use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Modules\Shared\Payment\Domain\Services\PaymentService;

class SyncPaymentStatusFromPaymentSystem extends BaseAction {

    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ) {}


    public function handle(Payment $payment): PaymentStatus {
        $paymentMethodType = $payment->payment_method;
        $paymentServiceClass = GetPaymentServiceByPaymentMethod::make()->handle($paymentMethodType);

        /** @var PaymentService $paymentService */
        $paymentService = new $paymentServiceClass($this->paymentRepository);
        $payment = $paymentService->syncFromPaymentSystemProvider($payment->id);
        return $payment->status;
    }

}
