<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Payment\Domain\Models\PaymentMethodsCollection;

interface PaymentMethodRepository extends BaseRepositoryInterface {

    public function getPaymentMethodsForMobileApp(): PaymentMethodsCollection;
    public function getPaymentMethodsForWeb(): PaymentMethodsCollection;
    public function getPaymentMethodsForAdminPanel(): PaymentMethodsCollection;
}
