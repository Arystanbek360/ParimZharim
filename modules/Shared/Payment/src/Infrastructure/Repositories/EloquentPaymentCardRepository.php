<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCard;
use Modules\Shared\Payment\Domain\Repositories\PaymentCardRepository;

class EloquentPaymentCardRepository extends BaseRepository implements PaymentCardRepository
{
    public function saveFromTipTopPayPayment(Payment $payment, array $paymentSystemData): PaymentCard
    {
        return PaymentCard::updateOrCreate(
            ['token' => $paymentSystemData['Token']],
            [
                'customer_id' => $payment->customer_id,
                'payment_id' => $payment->id,
                'card_first_six' => $paymentSystemData['CardFirstSix'] ?? null,
                'card_last_four' => $paymentSystemData['CardLastFour'] ?? null,
                'card_exp_date' => $paymentSystemData['CardExpDate'] ?? null,
                'card_type' => $paymentSystemData['CardType'] ?? null,
                'issuer' => $paymentSystemData['Issuer'] ?? null,
                'gateway_name' => $paymentSystemData['GatewayName'] ?? null,
                'is_active' => true,
            ]
        );
    }

    public function saveFromTipTopPayTokenListItem(int $customerId, array $tokenData): PaymentCard
    {
        $cardMask = $tokenData['CardMask'] ?? null;

        return PaymentCard::updateOrCreate(
            ['token' => $tokenData['Token']],
            [
                'customer_id' => $customerId,
                'card_mask' => $cardMask,
                'card_first_six' => $this->extractFirstSix($cardMask),
                'card_last_four' => $this->extractLastFour($cardMask),
                'expiration_date_month' => $tokenData['ExpirationDateMonth'] ?? null,
                'expiration_date_year' => $tokenData['ExpirationDateYear'] ?? null,
                'card_exp_date' => $this->formatExpirationDate($tokenData),
                'is_active' => true,
            ]
        );
    }

    public function getActiveCardsForCustomer(int $customerId): Collection
    {
        return PaymentCard::query()
            ->where('customer_id', $customerId)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getActiveCardForCustomer(int $cardId, int $customerId): ?PaymentCard
    {
        return PaymentCard::query()
            ->where('id', $cardId)
            ->where('customer_id', $customerId)
            ->where('is_active', true)
            ->first();
    }

    private function extractFirstSix(?string $cardMask): ?string
    {
        if (!$cardMask || !preg_match('/\d{6}/', $cardMask, $matches)) {
            return null;
        }

        return $matches[0];
    }

    private function extractLastFour(?string $cardMask): ?string
    {
        if (!$cardMask || !preg_match('/(\d{4})\D*$/', $cardMask, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function formatExpirationDate(array $tokenData): ?string
    {
        $month = $tokenData['ExpirationDateMonth'] ?? null;
        $year = $tokenData['ExpirationDateYear'] ?? null;

        if (!$month || !$year) {
            return null;
        }

        return sprintf('%02d/%02d', (int)$month, (int)$year % 100);
    }
}
