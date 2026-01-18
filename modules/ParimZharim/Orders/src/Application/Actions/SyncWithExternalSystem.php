<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;

class SyncWithExternalSystem extends BaseAction
{

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderID): void
    {
        $order = QueryOrderByID::make()->handle($orderID);
        $metadata = $order->metadata;
        $metadata['is_synced_in_external_system'] = true;
        $order->metadata = $metadata;
        $this->orderRepository->saveOrder($order);
    }
}
