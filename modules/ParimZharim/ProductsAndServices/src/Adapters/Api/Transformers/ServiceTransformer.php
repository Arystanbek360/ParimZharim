<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class ServiceTransformer extends BaseTransformer
{
    public function transform(Service|BaseDTO|BaseModel|BaseValueObject|array $data): array
    {
        //TODO: I dont know what is max_quantity, so I just put 50
        return [
            'id'   => $data->id,
            'name' => $data->name,
            'price' => (float) $data->price,
            'max_quantity' => 50,
        ];
    }
}
