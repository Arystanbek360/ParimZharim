<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServicesCollection;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Application\BaseAction;

class QueryOrderableServicesByOrderableServiceObjectID extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(?int $servicedObjectID = null): OrderableServicesCollection
    {
       if ($servicedObjectID == null) {
           return new OrderableServicesCollection();
        }

        return $this->orderableServiceObjectRepository->getOrderableServicesByOrderableServiceObjectID($servicedObjectID);
    }
}
