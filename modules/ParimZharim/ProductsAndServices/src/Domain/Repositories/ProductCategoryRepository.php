<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface ProductCategoryRepository extends BaseRepositoryInterface {

    public function getUsableProductCategories(): ProductCategoryCollection;

}
