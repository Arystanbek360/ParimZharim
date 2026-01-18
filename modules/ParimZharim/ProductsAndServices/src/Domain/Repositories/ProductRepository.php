<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface ProductRepository extends BaseRepositoryInterface {

    public function getProductsByCategory(int $categoryID): ProductCollection;

    public function getAllProducts(): ProductCollection;
}
