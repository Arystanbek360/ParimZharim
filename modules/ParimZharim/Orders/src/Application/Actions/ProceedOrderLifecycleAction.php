<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\ParimZharim\Orders\Domain\Models\OrderCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Throwable;
use function Sentry\captureException;

class ProceedOrderLifecycleAction extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->cancelOrders();
        $this->startOrders();
        $this->finishOrders();
    }

    private function getOrdersNotConfirmedAndMustBeCancelled(): OrderCollection
    {
        $createdOrdersToCancel = $this->orderRepository->getOrdersByStatus(OrderStatus::CREATED);
        $readyToCancelOrders = new OrderCollection();

        foreach ($createdOrdersToCancel as $order) {
            if ($order->confirm_before < Carbon::now()) {
                $readyToCancelOrders->add($order);
            }
        }

        return $readyToCancelOrders;
    }

    private function getOrdersMustBeFinished(): OrderCollection
    {
        $ordersToFinish = $this->orderRepository->getOrdersByStatus(OrderStatus::STARTED);
        $readyToFinishOrders = new OrderCollection();

        foreach ($ordersToFinish as $order) {
            if ($order->end_time <= Carbon::now()) {
                $readyToFinishOrders->add($order);
            }
        }

        return $readyToFinishOrders;
    }

    private function getOrdersMustBeStarted(): OrderCollection
    {
        $ordersToStart = $this->orderRepository->getOrdersByStatus(OrderStatus::CONFIRMED);
        $readyToStartOrders = new OrderCollection();

        foreach ($ordersToStart as $order) {
            if ($order->start_time <= Carbon::now()) {
                $readyToStartOrders->add($order);
            }
        }

        return $readyToStartOrders;
    }

    private function cancelOrders(): void
    {
        $orders = $this->getOrdersNotConfirmedAndMustBeCancelled();
        foreach ($orders as $order) {
            try {
                CancelOrder::make()->handle($order->id);
            } catch (Throwable $e) {
                Log::error("Error while cancelling order: " . $order->id);
                Log::error($e->getMessage());
                captureException($e);
            }
        }
    }

    private function startOrders(): void
    {
        $orders = $this->getOrdersMustBeStarted();
        foreach ($orders as $order) {
            try {
                StartOrder::make()->handle($order->id);
            } catch (Throwable $e) {
                Log::error("Error while starting order: " . $order->id);
                Log::error($e->getMessage());
                captureException($e);
            }
        }
    }

    private function finishOrders(): void
    {
        $orders = $this->getOrdersMustBeFinished();
        foreach ($orders as $order) {
            try {
                FinishOrder::make()->handle($order->id, false);
            } catch (Throwable $e) {
                Log::error("Error while finishing order: " . $order->id);
                Log::error($e->getMessage());
                captureException($e);
            }
        }
    }
}
