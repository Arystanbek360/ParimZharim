<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentMethod;
use Modules\Shared\Payment\Domain\Models\PaymentMethodsCollection;
use Modules\Shared\Payment\Domain\Repositories\PaymentMethodRepository;
use Modules\Shared\Payment\Infrastructure\Repositories\EloquentPaymentMethodRepository;
use Modules\Shared\Payment\Infrastructure\Repositories\EloquentPaymentRepository;

class GetPaymentsMethodsForAdminPanel extends BaseAction {

    public function __construct(
        private readonly PaymentMethodRepository $paymentMethodRepository
    ) {}

    public function handle(): PaymentMethodsCollection
    {
        return $this->paymentMethodRepository->getPaymentMethodsForAdminPanel();
    }
}
