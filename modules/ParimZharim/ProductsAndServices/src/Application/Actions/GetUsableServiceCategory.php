<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceCategoryRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetUsableServiceCategory extends BaseAction {

    public function __construct(
        private readonly ServiceCategoryRepository $serviceCategoryRepository
    )
    {}
    public function handle(): ServiceCategoryCollection
    {
        return $this->serviceCategoryRepository->getUsableServiceCategories();
    }
}
