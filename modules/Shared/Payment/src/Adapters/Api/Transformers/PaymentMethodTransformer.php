<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Api\Transformers;

use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;
use Modules\Shared\Payment\Domain\Models\PaymentMethod;

class PaymentMethodTransformer extends BaseTransformer
{

    public function transform(PaymentMethod|BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        return[
            'name' => $data->type->value,
            'is_available' => $data->is_available_for_mobile,
        ];
    }
}
