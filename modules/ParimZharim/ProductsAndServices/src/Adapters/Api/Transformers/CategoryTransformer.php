<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class CategoryTransformer extends BaseTransformer
{

    public function transform(ProductCategory|ServiceCategory|BaseDTO|BaseModel|BaseValueObject|array $data): array
    {
        return [
            'id'   => $data->id,
            'name' => $data->name
        ];
    }
}
