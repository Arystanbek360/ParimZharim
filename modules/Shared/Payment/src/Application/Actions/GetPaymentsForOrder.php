<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;

class GetPaymentsForOrder extends BaseAction {

    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ) {}

    public function handle(int $orderID): PaymentCollection
    {
        return $this->paymentRepository->getPaymentsForOrder($orderID);
    }
}
