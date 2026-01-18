<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\LoyaltyProgram\Application\Actions\RecalculateLoyaltyProgramCustomerDiscount;
use Modules\ParimZharim\LoyaltyProgram\Domain\Errors\LoyaltyProgramCustomerNotFound;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Throwable;

class CompleteOrder extends BaseAction
{

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    /**
     * @throws OrderNotFound
     * @throws LoyaltyProgramCustomerNotFound
     * @throws StatusChangeViolation|Throwable
     */
    public function handle(int $orderID): void
    {
        $order = QueryOrderByID::make()->handle($orderID);

        // available only for FINISHED orders
        if ($order->status !== OrderStatus::FINISHED) {
            throw new StatusChangeViolation(
                wishStatus: OrderStatus::COMPLETED,
                currentStatus: $order->status
            );
        }

        DB::beginTransaction();
        try {
            $this->orderRepository->changeOrderStatus($orderID, OrderStatus::COMPLETED);
            RecalculateLoyaltyProgramCustomerDiscount::make()->handle($order->customer_id);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
