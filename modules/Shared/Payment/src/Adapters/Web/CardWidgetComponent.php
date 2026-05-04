<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Web;

use Illuminate\View\View;
use Throwable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Modules\Shared\Core\Adapters\Web\BaseUIComponent;
use Modules\Shared\Payment\Application\Actions\GetSavedPaymentCardsForCustomer;
use Modules\Shared\Payment\Application\Actions\PayPaymentWithSavedCard;
use Modules\Shared\Payment\Application\Actions\QueryPaymentByID;
use Modules\Shared\Payment\Application\Actions\SyncSavedPaymentCardsFromTipTopPay;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Services\CloudPaymentServiceInterface;


class CardWidgetComponent extends BaseUIComponent
{

    public Payment $payment;
    public array $paymentData = [];
    public array $savedCards = [];
    public ?string $savedCardPaymentError = null;
    public ?string $savedCardPaymentSuccess = null;

    protected CloudPaymentServiceInterface $paymentService;

    private function getPaymentService(): CloudPaymentServiceInterface
    {
        if (!isset($this->paymentService)) {
            $this->paymentService = app(CloudPaymentServiceInterface::class);
        }
        return $this->paymentService;
    }


    public function mount(int $paymentID): void
    {
        $payment = QueryPaymentByID::make()->handle($paymentID);
        if (!$payment) {
            abort(404);
        }
        $this->payment = $payment;
        $this->paymentData = $this->getPaymentService()->getPaymentData($payment);
        $this->syncAndLoadSavedCards();
    }

    public function payWithSavedCard(int $paymentCardId): void
    {
        $this->savedCardPaymentError = null;
        $this->savedCardPaymentSuccess = null;

        try {
            $this->payment = PayPaymentWithSavedCard::make()->handle($this->payment, $paymentCardId);
            $this->paymentData = $this->getPaymentService()->getPaymentData($this->payment);

            if ($this->payment->status === PaymentStatus::PENDING && !empty($this->payment->metadata['threeDs'])) {
                $this->redirectRoute('payment.tiptoppay.3ds', ['paymentID' => $this->payment->id]);
                return;
            }

            if (in_array($this->payment->status, [PaymentStatus::COMPLETED, PaymentStatus::SUCCESS], true)) {
                $this->savedCardPaymentSuccess = 'Оплата прошла успешно.';
                return;
            }

            $this->savedCardPaymentError = 'Не удалось оплатить сохраненной картой. Попробуйте другую карту или введите новую.';
        } catch (Throwable) {
            $this->savedCardPaymentError = 'Не удалось оплатить сохраненной картой. Попробуйте другую карту или введите новую.';
        }
    }

    #[Title('Форма оплаты')]
    #[Layout('payment::components.layouts.app')]
    public function render(): View
    {
        return view($this->getPaymentService()->getPaymentForm($this->payment));
    }

    private function syncAndLoadSavedCards(): void
    {
        try {
            SyncSavedPaymentCardsFromTipTopPay::make()->handle($this->payment->customer_id);
        } catch (Throwable) {
        }

        $this->savedCards = GetSavedPaymentCardsForCustomer::make()
            ->handle($this->payment->customer_id)
            ->map(fn ($card) => [
                'id' => $card->id,
                'card_mask' => $card->card_mask,
                'card_last_four' => $card->card_last_four,
                'card_exp_date' => $card->card_exp_date,
                'card_type' => $card->card_type,
            ])
            ->values()
            ->all();
    }

}
