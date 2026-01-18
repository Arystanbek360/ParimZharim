<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface ServiceCategoryRepository extends BaseRepositoryInterface {
    public function getUsableServiceCategories(): ServiceCategoryCollection;
}
