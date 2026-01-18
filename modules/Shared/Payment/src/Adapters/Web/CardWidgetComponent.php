<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Web;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Modules\Shared\Core\Adapters\Web\BaseUIComponent;
use Modules\Shared\Payment\Application\Actions\QueryPaymentByID;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Services\CloudPaymentServiceInterface;


class CardWidgetComponent extends BaseUIComponent
{

    public Payment $payment;
    public array $paymentData = [];

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
    }

    #[Title('Форма оплаты')]
    #[Layout('payment::components.layouts.app')]
    public function render(): View
    {
        return view($this->getPaymentService()->getPaymentForm($this->payment));
    }

}
