<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Application\NotifyAllAdmins;
use Throwable;

class FinishOrder extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderID, bool $beforeEnd): void
    {
        $order = QueryOrderByID::make()->handle($orderID);

        // available only for STARTED orders
        if ($order->status !== OrderStatus::STARTED) {
            throw new StatusChangeViolation(
                wishStatus: OrderStatus::FINISHED,
                currentStatus: $order->status
            );
        }

        DB::beginTransaction();
        try {
            $this->orderRepository->changeOrderStatus($orderID, OrderStatus::FINISHED);

            if ($beforeEnd) {
                $order->end_time = Carbon::now()->ceilMinutes(30);
                $this->orderRepository->saveOrderQuietly($order);
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if (!$beforeEnd) {
            NotifyAllAdmins::make()->handle("Время по заказу истекло. Заказ № {$orderID} завершен автоматически");
        } else {
            NotifyAllAdmins::make()->handle("Заказ № {$orderID} завершен раньше времени");
        }

    }
}
