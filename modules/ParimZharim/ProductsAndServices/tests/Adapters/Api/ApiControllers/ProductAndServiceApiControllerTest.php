<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Adapters\Api\ApiControllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Middleware\HandleCors;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\GetUsableProductCategory;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\GetUsableServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\QueryProductsWithFilters;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Application\Actions\QueryServicesWithFilters;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\ApiControllers\ProductAndServiceApiController;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;
use Mockery;

class ProductAndServiceApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Route::prefix('api/products-services')->middleware(HandleCors::class)->group(function () {
            Route::get('/get-product-categories', [ProductAndServiceApiController::class, 'getProductCategories'])->name('api.products-services.get-product-categories');
            Route::get('/get-products-by-category', [ProductAndServiceApiController::class, 'getProductsByCategory'])->name('api.products-services.get-products-by-category');
            Route::get('/get-service-categories', [ProductAndServiceApiController::class, 'getServiceCategories'])->name('api.products-services.get-service-categories');
            Route::get('/get-services-by-category', [ProductAndServiceApiController::class, 'getServicesByCategory'])->name('api.products-services.get-services-by-category');
            Route::get('/get-all-products-grouped-by-category', [ProductAndServiceApiController::class, 'getAllProductsGroupedByCategory'])->name('api.products-services.get-all-products-grouped-by-category');
        });
    }


    public function test_get_product_categories()
    {
        $this->mock(GetUsableProductCategory::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(new ProductCategoryCollection([
                (new ProductCategory())->forceFill(['id' => 1, 'name' => 'Category 1']),
                (new ProductCategory())->forceFill(['id' => 2, 'name' => 'Category 2']),
            ]));
        });

        $response = $this->getJson(route('api.products-services.get-product-categories'));

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonStructure([
            '*' => ['id', 'name']
        ]);
        $response->assertJson([
            ['id' => 1, 'name' => 'Category 1'],
            ['id' => 2, 'name' => 'Category 2']
        ]);
    }

    public function test_get_service_categories()
    {
        $this->mock(GetUsableServiceCategory::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturn(new ServiceCategoryCollection([
                (new ServiceCategory())->forceFill(['id' => 1, 'name' => 'Category 1']),
                (new ServiceCategory())->forceFill(['id' => 2, 'name' => 'Category 2']),
            ]));
        });

        $response = $this->getJson(route('api.products-services.get-service-categories'));

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonStructure([
            '*' => ['id', 'name']
        ]);
        $response->assertJson([
            ['id' => 1, 'name' => 'Category 1'],
            ['id' => 2, 'name' => 'Category 2']
        ]);
    }


    public function test_get_products_by_category()
    {
        $this->mock(QueryProductsWithFilters::class, function ($mock) {
            $mock->shouldReceive('handle')->with(1)->andReturn(new ProductCollection([
                (new Product())->forceFill(['id' => 1, 'name' => 'Product 1', 'description' => 'Description 1', 'price' => 100, 'photo' => null, 'category_id' => null]),
                (new Product())->forceFill(['id' => 2, 'name' => 'Product 2', 'description' => 'Description 2', 'price' => 200, 'photo' => null, 'category_id' => null]),
            ]));
        });

        $response = $this->getJson(route('api.products-services.get-products-by-category', ['category_id' => 1]));

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'description', 'price', 'photo', 'category_id']
        ]);
        $response->assertJson([
            ['id' => 1, 'name' => 'Product 1', 'description' => 'Description 1', 'price' => 100, 'photo' => null, 'category_id' => null],
            ['id' => 2, 'name' => 'Product 2', 'description' => 'Description 2', 'price' => 200, 'photo' => null, 'category_id' => null]
        ]);
    }
    public function test_get_services_by_category()
    {
        // Создаем сервисы через фабрику и сохраняем их в базе данных
        $service1 = Service::factory()->create(['id' => 1, 'name' => 'Service 1', 'price' => 100, 'service_category_id' => 1 ]);
        $service2 = Service::factory()->create(['id' => 2, 'name' => 'Service 2', 'price' => 200, 'service_category_id' => 1 ]);
    
        $serviceCollection = new ServiceCollection([$service1, $service2]);

        $mock = Mockery::mock(QueryServicesWithFilters::class);
        $this->app->instance(QueryServicesWithFilters::class, $mock);
        
        $mock->shouldReceive('handle')
             ->once()
             ->with(1, null)
             ->andReturn($serviceCollection);
    
        $response = $this->getJson(route('api.products-services.get-services-by-category', ['category_id' => 1]));
        $response->assertStatus(200);
        $response->assertJson([
            ['id' => $service1->id, 'name' => 'Service 1', 'price' => 100, 'max_quantity' => 50],
            ['id' => $service2->id, 'name' => 'Service 2', 'price' => 200, 'max_quantity' => 50]
        ]);
    
    }
    
    public function test_get_all_products_grouped_by_category()
    {
    
        // Мокируем QueryProductsWithFilters для возвращения ProductCollection
        $this->mock(QueryProductsWithFilters::class, function ($mock) {
            $productCollection = new ProductCollection([
                new Product(['id' => 1, 'name' => 'Product 1', 'description' => 'Description 1', 'price' => 100, 'photo' => null, 'product_category_id' => 1]),
                new Product(['id' => 2, 'name' => 'Product 2', 'description' => 'Description 2', 'price' => 200, 'photo' => null, 'product_category_id' => 2]),
            ]);
            $mock->shouldReceive('handle')->andReturn($productCollection);
        });
    
        // Мокируем GetUsableProductCategory для возвращения ProductCategoryCollection
        $this->mock(GetUsableProductCategory::class, function ($mock) {
            $categoryCollection = new ProductCategoryCollection([
                new ProductCategory(['id' => 1, 'name' => 'Category 1']),
                new ProductCategory(['id' => 2, 'name' => 'Category 2']),
            ]);
            $mock->shouldReceive('handle')->andReturn($categoryCollection);
        });
    
        $response = $this->getJson(route('api.products-services.get-all-products-grouped-by-category'));
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonStructure([
            'categories' => [
                '*' => [
                    'id', 'name', 'items' => [
                        '*' => [
                            'id', 'name', 'description', 'price', 'photo', 'product_category_id'
                        ]
                    ]
                ]
            ]
        ]);
    
    }
 
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
