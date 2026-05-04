<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Domain\Repositories\PaymentCardRepository;
use Modules\Shared\Payment\Infrastructure\Services\TipTopPayPaymentService;

class SyncSavedPaymentCardsFromTipTopPay extends BaseAction
{
    public function __construct(
        private readonly PaymentCardRepository $paymentCardRepository,
        private readonly TipTopPayPaymentService $paymentService,
    ) {}

    public function handle(int $customerId): void
    {
        $tokens = $this->paymentService->getTokensForAccount((string)$customerId);

        foreach ($tokens as $tokenData) {
            $this->paymentCardRepository->saveFromTipTopPayTokenListItem($customerId, $tokenData);
        }
    }
}
