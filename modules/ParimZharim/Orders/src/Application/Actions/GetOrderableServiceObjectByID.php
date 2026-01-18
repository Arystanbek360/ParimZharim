<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;


use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetOrderableServiceObjectByID extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(int $orderableServiceObjectID): ?OrderableServiceObject
    {
        return $this->orderableServiceObjectRepository->getOrderableServiceObjectById($orderableServiceObjectID);
    }

}
