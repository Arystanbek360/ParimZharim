<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceRepository;
use Modules\Shared\Core\Application\BaseAction;

class QueryServicesWithFilters extends BaseAction {

    public function __construct(
        private readonly ServiceRepository $serviceRepository
    )
    {}

    public function handle(?int $categoryID = null): ServiceCollection
    {
        if ($categoryID !== null) {
            return $this->serviceRepository->getServicesByCategory($categoryID);
        }

        return $this->serviceRepository->getAllServices();
    }
}
