<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductRepository;
use Modules\Shared\Core\Application\BaseAction;

class QueryProductsWithFilters extends BaseAction {

    public function __construct(
        private readonly ProductRepository $productRepository
    )
    {}

    public function handle(?int $categoryID = null): ProductCollection
    {
        if ($categoryID !== null) {
            return $this->productRepository->getProductsByCategory($categoryID);
        }

        return $this->productRepository->getAllProducts();  // This method needs to be added to the repository
    }
}
