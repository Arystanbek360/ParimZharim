<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Repositories;

use Illuminate\Support\Collection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCard;

interface PaymentCardRepository extends BaseRepositoryInterface
{
    public function saveFromTipTopPayPayment(Payment $payment, array $paymentSystemData): PaymentCard;

    public function saveFromTipTopPayTokenListItem(int $customerId, array $tokenData): PaymentCard;

    public function getActiveCardsForCustomer(int $customerId): Collection;

    public function getActiveCardForCustomer(int $cardId, int $customerId): ?PaymentCard;
}
