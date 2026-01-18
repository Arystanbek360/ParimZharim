<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Api\Transformers;

use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

class PaymentTransformer extends BaseTransformer
{

    public function transform(Payment|BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        $url = null;
        if ($data->payment_method == PaymentMethodType::CLOUD_PAYMENT && $data->status == PaymentStatus::CREATED) {
            $url = config('app.url') . '/payment-widget/' . $data->id;
        }
        if ($data instanceof Payment) {
            return [
                'id' => $data->id,
                'status' => $data->status,
                'amount' => (float) $data->total,
                'url' => $url,
            ];
        }
        return [];
    }
}
