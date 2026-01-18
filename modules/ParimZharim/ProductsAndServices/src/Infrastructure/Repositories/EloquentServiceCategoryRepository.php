<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceCategoryRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;

class EloquentServiceCategoryRepository extends BaseRepository implements ServiceCategoryRepository {

    public function getUsableServiceCategories(): ServiceCategoryCollection
    {
        $categories = ServiceCategory::whereHas('services')
            ->where('is_visible_to_customers', '=', true)
            ->get();

        return new ServiceCategoryCollection($categories->all());
    }
}
