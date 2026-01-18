<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ParimZharim\Orders\Application\ApplicationError\AdvancePaymentIsAlreadyCreated;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Application\ApplicationError\WrongActualAdvancedPayment;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Application\Actions\CreatePayment;
use Modules\Shared\Payment\Application\DTO\PaymentData;
use Modules\Shared\Payment\Application\DTO\PaymentItemData;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Throwable;

class CreateAdvancePaymentForOrder extends BaseAction
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly PaymentRepository $paymentRepository
    ){}

    /**
     * @throws Throwable
     * @throws WrongActualAdvancedPayment
     * @throws AdvancePaymentIsAlreadyCreated
     * @throws StatusChangeViolation
     * @throws OrderNotFound
     */
    public function handle(int $orderID, float $price, PaymentMethodType $paymentMethodType): Payment
    {
        DB::beginTransaction();
        try {
            $order = QueryOrderByID::make()->handle($orderID);
            if ($order->status !== OrderStatus::CREATED) {
                throw new StatusChangeViolation(
                    wishStatus: OrderStatus::CONFIRMED,
                    currentStatus: $order->status
                );
            }

            $this->updateOrderMetadata($order, $price);
            $payment = $this->createPayment($order, $price, $paymentMethodType);
            $this->orderRepository->saveOrderQuietly($order);
            DB::commit();
            return $payment;
        } catch (OrderNotFound|StatusChangeViolation|WrongActualAdvancedPayment|Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws WrongActualAdvancedPayment
     */
    private function updateOrderMetadata($order, $advancePayment): void
    {
        $metadata = $order->metadata;
        $expectedAdvancePayment = (int)$metadata['expectedAdvancePayment'];
        if ($advancePayment < $expectedAdvancePayment) {
            throw new WrongActualAdvancedPayment($expectedAdvancePayment);
        }
        $metadata['actualAdvancePayment'] = $advancePayment;
        $order->metadata = $metadata;
    }

    /**
     * @throws AdvancePaymentIsAlreadyCreated
     */
    private function createPayment($order, $price, $paymentMethodType): Payment
    {
        if ($paymentMethodType === PaymentMethodType::CLOUD_PAYMENT) {
            $createdPayment = $this->paymentRepository->findPaymentsByOrderAndStatus($order->id, [PaymentStatus::CREATED], $paymentMethodType);
            if ($createdPayment->isNotEmpty()) {
                Log::info("Payment for order {$order->id} is already created");
                return $createdPayment->first();
            }
        }

        $payments = $this->paymentRepository->findPaymentsByOrderAndStatus($order->id, [PaymentStatus::PENDING, PaymentStatus::SUCCESS, PaymentStatus::COMPLETED]);

        if ($payments->isNotEmpty()) {
            Log::info("Payment for order {$order->id} is already created");
            throw new AdvancePaymentIsAlreadyCreated();
        }

        $paymentItemData = new PaymentItemData(
            name: "Предоплата заказа №" . $order->id,
            price: $price,
            quantity: 1
        );

        $paymentData = new PaymentData(
            orderID: $order->id,
            customerID: $order->customer_id,
            paymentMethodType: $paymentMethodType,
            comment: "Предоплата заказа №" . $order->id,
            items: [$paymentItemData]
        );

        Log::info("Creating payment for order {$order->id}");

        return CreatePayment::make()->handle($paymentData);
    }
}
