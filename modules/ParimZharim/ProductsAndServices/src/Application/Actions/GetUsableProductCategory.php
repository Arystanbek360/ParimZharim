<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductCategoryRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetUsableProductCategory extends BaseAction {

    public function __construct(
        private readonly ProductCategoryRepository $productCategoryRepository
    )
    {}

    public function handle(): ProductCategoryCollection
    {
        return $this->productCategoryRepository->getUsableProductCategories();
    }
}
