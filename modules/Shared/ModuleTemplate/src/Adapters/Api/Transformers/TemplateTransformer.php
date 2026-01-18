<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Adapters\Api\Transformers;

use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class TemplateTransformer extends BaseTransformer
{

    public function transform(BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        // TODO: Implement transform() method.
    }
}
