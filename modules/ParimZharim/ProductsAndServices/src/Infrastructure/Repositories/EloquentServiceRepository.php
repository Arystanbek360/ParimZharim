<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;

class EloquentServiceRepository extends BaseRepository implements ServiceRepository {

    public function getServicesByCategory(int $categoryID): ServiceCollection
    {
       $services = Service::where('service_category_id', $categoryID)
           ->where('is_active', '=', true)
           ->get();

       return new ServiceCollection($services->all());
    }

    public function getAllServices(): ServiceCollection
    {
        $services = Service::where('is_active', '=', true)->get();
        return new ServiceCollection($services->all());
    }
}
