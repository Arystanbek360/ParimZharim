<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Events;

use Modules\Shared\Core\Domain\BaseEvent;
use Modules\Shared\Payment\Domain\Models\Payment;

class PaymentFailed extends BaseEvent {
    public function __construct(public Payment $payment, public ?string $reason = null) {}
}
