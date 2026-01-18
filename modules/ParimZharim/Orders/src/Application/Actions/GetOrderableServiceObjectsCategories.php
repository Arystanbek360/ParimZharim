<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Collection;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetOrderableServiceObjectsCategories extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(): Collection
    {
        $objects = $this->orderableServiceObjectRepository->getAllOrderableServiceObjectCollection();
        return $objects->pluck('category')->unique();
    }
}
