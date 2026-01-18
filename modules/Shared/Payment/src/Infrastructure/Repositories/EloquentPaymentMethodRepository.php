<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Infrastructure\Repositories;

use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentMethod;
use Modules\Shared\Payment\Domain\Models\PaymentMethodsCollection;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentMethodRepository;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;

class EloquentPaymentMethodRepository extends BaseRepository implements PaymentMethodRepository {

    public function getPaymentMethodsForMobileApp(): PaymentMethodsCollection {
        $paymentMethods = PaymentMethod::where('is_available_for_mobile', true)->get();
        return new PaymentMethodsCollection($paymentMethods);
    }

    public function getPaymentMethodsForWeb(): PaymentMethodsCollection
    {
        $paymentMethods = PaymentMethod::where('is_available_for_web', true)->get();
        return new PaymentMethodsCollection($paymentMethods);
    }

    public function getPaymentMethodsForAdminPanel(): PaymentMethodsCollection
    {
        $paymentMethods = PaymentMethod::where('is_available_for_admin', true)->get();
        return new PaymentMethodsCollection($paymentMethods);
    }
}
