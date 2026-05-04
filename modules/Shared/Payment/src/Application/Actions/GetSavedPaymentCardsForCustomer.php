<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Illuminate\Support\Collection;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Repositories\PaymentCardRepository;

class GetSavedPaymentCardsForCustomer extends BaseAction
{
    public function __construct(
        private readonly PaymentCardRepository $paymentCardRepository,
    ) {}

    public function handle(int $customerId): Collection
    {
        return $this->paymentCardRepository->getActiveCardsForCustomer($customerId);
    }
}
