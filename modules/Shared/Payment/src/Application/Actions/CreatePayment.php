<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Application\DTO\PaymentData;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentItem;
use Modules\Shared\Payment\Domain\Models\PaymentItemCollection;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;


class CreatePayment extends BaseAction {

    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ) {}


    public function handle(PaymentData $paymentData): Payment {
        return $this->createPayment($paymentData);
    }

    private function createPayment(PaymentData $paymentData): Payment
    {
        $payment = new Payment();
        $payment->payable_order_id = $paymentData->orderID;
        $payment->customer_id = $paymentData->customerID;
        $payment->payment_method = $paymentData->paymentMethodType;
        $payment->comment = $paymentData->comment;
        $payment->status = match ($paymentData->paymentMethodType) {
            PaymentMethodType::CASH, PaymentMethodType::KASPI => PaymentStatus::PENDING,
            PaymentMethodType::CLOUD_PAYMENT => PaymentStatus::CREATED,
        };
        $payment->total = 0;
        $paymentItems = new PaymentItemCollection();
        foreach ($paymentData->items as $item) {
            $paymentItem = new PaymentItem([
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ]);
            $paymentItems->add($paymentItem);
            $payment->total += $item->price * $item->quantity;
        }
        $this->paymentRepository->savePayment($payment, $paymentItems);

        return $payment;
    }


}
