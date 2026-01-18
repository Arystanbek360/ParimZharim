<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\CategoryTransformer;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\ProductTransformer;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\ServiceTransformer;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Illuminate\Http\Request;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\GetUsableProductCategory;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\GetUsableServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\QueryProductsWithFilters;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\QueryServicesWithFilters;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;
use Illuminate\Support\Facades\Log;

class ProductAndServiceApiController extends BaseApiController {


    /**
     * @throws InvalidDataTransformer
     */
    public function getProductCategories(): JsonResponse
    {
        $categories = GetUsableProductCategory::make()->handle();
        $this->setTransformer(new CategoryTransformer());
        return $this->respondWithTransformer($categories);
    }

    /**
     * @throws InvalidDataTransformer
     */
    public function getServiceCategories(): JsonResponse
    {
        $categories = GetUsableServiceCategory::make()->handle();
        $this->setTransformer(new CategoryTransformer());
        return $this->respondWithTransformer($categories);
    }

    /**
     * @throws InvalidDataTransformer
     */
    public function getProductsByCategory(Request $request): JsonResponse
    {
        $categoryID = $request->query('category_id', null);
        $categoryID = is_numeric($categoryID) ? (int) $categoryID : null;
        $products = QueryProductsWithFilters::make()->handle($categoryID);
        $this->setTransformer(new ProductTransformer());
        return $this->respondWithTransformer($products);
    }

    /**
     * @throws InvalidDataTransformer
     */
    public function getServicesByCategory(Request $request): JsonResponse
    {
        $categoryID = $request->query('category_id', null);
        $categoryID = is_numeric($categoryID) ? (int) $categoryID : null;
        $services = QueryServicesWithFilters::make()->handle($categoryID, null);
        $this->setTransformer(new ServiceTransformer());
        return $this->respondWithTransformer($services);
    }

    public function getAllProductsGroupedByCategory(): JsonResponse
    {
        // Fetch all products regardless of category
        $allProducts = QueryProductsWithFilters::make()->handle(null);
    
        // Make sure to convert to a collection if not already one
        $allProducts = collect($allProducts);
    
        // Retrieve all categories
        $categories = GetUsableProductCategory::make()->handle();
    
        // Convert to collection if not already one
        $categories = collect($categories);
    
        // Group products by category
        $groupedProducts = $categories->map(function ($category) use ($allProducts) {
            $transformer = new ProductTransformer();
            $filteredProducts = $allProducts->filter(function ($product) use ($category) {
                return $product->product_category_id === $category->id;
            })->values(); // Reset the array keys and prepare for JSON conversion
    
            // Manually transform each product in the filtered collection
            $transformedProducts = $filteredProducts->map(function ($product) use ($transformer) {
                return $transformer->transform($product);
            });
    
            return [
                'id' => $category->id,
                'name' => $category->name,
                'items' => $transformedProducts
            ];
        });
    
        // Respond with grouped products
        return $this->respond(['categories' => $groupedProducts]);
    }
    

    private function setTransformer($transformer): void
    {
        $this->transformer = $transformer;
    }
}
