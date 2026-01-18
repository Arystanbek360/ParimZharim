<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Payment\Application\Actions\CancelPayment;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Throwable;

class CancelOrder extends BaseAction
{

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    /**
     * @throws OrderNotFound
     * @throws Throwable
     * @throws StatusChangeViolation
     */
    public function handle(int $orderID): void
    {
        $order = QueryOrderByID::make()->handle($orderID);

        if (!in_array($order->status, [OrderStatus::CREATED, OrderStatus::CONFIRMED, OrderStatus::CANCELLATION_REQUESTED])) {
            throw new StatusChangeViolation(
                wishStatus: OrderStatus::CANCELLED,
                currentStatus: $order->status
            );
        }

        $payments = $order->payments()->whereIn('status', [PaymentStatus::CREATED, PaymentStatus::PENDING, PaymentStatus::SUCCESS])->get();

        try {
            DB::beginTransaction();
            if ($payments->isNotEmpty()) {
                foreach ($payments as $payment) {
                    CancelPayment::make()->handle($payment);
                }
            }
            $this->orderRepository->changeOrderStatus($orderID, OrderStatus::CANCELLED);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
