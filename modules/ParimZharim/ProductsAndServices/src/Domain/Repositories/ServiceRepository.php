<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface ServiceRepository extends BaseRepositoryInterface {

    public function getServicesByCategory(int $categoryID): ServiceCollection;

    public function getAllServices(): ServiceCollection;
}
