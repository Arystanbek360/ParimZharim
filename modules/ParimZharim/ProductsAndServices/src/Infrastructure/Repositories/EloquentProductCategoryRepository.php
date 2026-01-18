<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories;


use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductCategoryRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;


class EloquentProductCategoryRepository extends BaseRepository implements ProductCategoryRepository {

    public function getUsableProductCategories(): ProductCategoryCollection
    {
        $categories = ProductCategory::whereHas('products')
            ->where('is_visible_to_customers', '=', true)
            ->get();

        return new ProductCategoryCollection($categories->all());
    }
}
