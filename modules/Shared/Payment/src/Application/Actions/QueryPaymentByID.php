<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;

class QueryPaymentByID extends BaseAction {

    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ) {}

    public function handle(int $paymentID): ?Payment
    {
        return $this->paymentRepository->getPaymentById($paymentID);
    }
}
