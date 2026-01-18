<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;

class EloquentProductRepository extends BaseRepository implements ProductRepository {
    public function getProductsByCategory(int $categoryID): ProductCollection
    {
        $products = Product::where('product_category_id', $categoryID)
            ->where('is_active', '=', true)
            ->get();

        return new ProductCollection($products->all());
    }

    public function getAllProducts(): ProductCollection
    {
        $products = Product::where('is_active', '=', true)
            ->get();

        return new ProductCollection($products->all());
    }
}
