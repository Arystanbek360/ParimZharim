<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;


use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;

class ProceedPaymentLifecycle extends BaseAction {


    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ) {}


    public function handle(): void {
        $payments = $this->getPaymentsToSync();
        $this->syncPayments($payments);
    }


    private function syncPayments(PaymentCollection $paymentCollection): void {
        foreach ($paymentCollection as $payment) {
            SyncPaymentStatusFromPaymentSystem::make()->handle($payment);
        }
    }


    public function getPaymentsToSync(): PaymentCollection {
        $createdPayments = $this->paymentRepository->getPaymentsByStatus(PaymentStatus::CREATED);
        $pendingPayments = $this->paymentRepository->getPaymentsByStatus(PaymentStatus::PENDING);

        return $createdPayments->merge($pendingPayments);
    }
}
