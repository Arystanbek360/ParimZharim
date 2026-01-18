<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ServiceCollectionTest extends TestCase
{
    /** @test */
    public function it_can_add_services_to_collection()
    {
        $service1 = new Service(['name' => 'Service 1']);
        $service2 = new Service(['name' => 'Service 2']);

        $collection = new ServiceCollection([$service1, $service2]);

        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($service1));
        $this->assertTrue($collection->contains($service2));
    }

    /** @test */
    public function it_can_be_filtered()
    {
        $service1 = new Service(['name' => 'Service 1']);
        $service2 = new Service(['name' => 'Service 2']);
        $collection = new ServiceCollection([$service1, $service2]);

        $filtered = $collection->filter(function ($service) {
            return $service->name === 'Service 1';
        });

        $this->assertCount(1, $filtered);
        $this->assertTrue($filtered->contains($service1));
        $this->assertFalse($filtered->contains($service2));
    }

    /** @test */
    public function it_can_be_transformed()
    {
        $service1 = new Service(['name' => 'Service 1']);
        $service2 = new Service(['name' => 'Service 2']);
        $collection = new ServiceCollection([$service1, $service2]);

        $transformed = $collection->map(function ($service) {
            return strtoupper($service->name);
        });

        $this->assertEquals(['SERVICE 1', 'SERVICE 2'], $transformed->toArray());
    }
}
