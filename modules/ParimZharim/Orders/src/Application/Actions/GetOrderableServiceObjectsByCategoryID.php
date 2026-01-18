<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;


use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObjectsCollection;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetOrderableServiceObjectsByCategoryID extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(?int $categoryID): OrderableServiceObjectsCollection
    {
        return $this->orderableServiceObjectRepository->getOrderableServiceObjectCollectionByCategory($categoryID);
    }

}
