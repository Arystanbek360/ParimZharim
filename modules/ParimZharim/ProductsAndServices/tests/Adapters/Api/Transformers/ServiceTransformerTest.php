<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Tests\Adapters\Api\Transformers;

use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\ServiceTransformer;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ServiceTransformerTest extends TestCase
{
    public function test_transform_service()
    {
        // Создание сервиса с принудительным заполнением полей
        $service = (new Service())->forceFill([
            'id' => 1,
            'name' => 'Test Service',
            'price' => 100.00,
            'max_quantity' => 50  // Убедитесь, что max_quantity установлен
        ]);

        $transformer = new ServiceTransformer();
        $result = $transformer->transform($service);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Service', $result['name']);
        $this->assertEquals(100.00, $result['price']);
        $this->assertEquals(50, $result['max_quantity']);
    }

    public function test_transform_service_with_different_price()
    {
        $service = (new Service())->forceFill([
            'id' => 2,
            'name' => 'Another Service',
            'price' => 200.50,
            'max_quantity' => 50  // Подтвердите, что это значение установлено
        ]);

        $transformer = new ServiceTransformer();
        $result = $transformer->transform($service);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('Another Service', $result['name']);
        $this->assertEquals(200.50, $result['price']);
        $this->assertEquals(50, $result['max_quantity']);
    }
}
