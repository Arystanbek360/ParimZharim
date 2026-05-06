<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Api\Transformers;

use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;
use Modules\Shared\Payment\Domain\Models\PaymentCard;

class PaymentCardTransformer extends BaseTransformer
{
    public function transform(PaymentCard|BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        return [
            'id'             => $data->id,
            'card_mask'      => $this->buildMask($data),
            'card_last_four' => $data->card_last_four,
            'card_exp_date'  => $data->card_exp_date,
            'card_type'      => $data->card_type,
            'issuer'         => $data->issuer,
        ];
    }

    private function buildMask(PaymentCard $card): string
    {
        if ($card->card_mask) {
            return $card->card_mask;
        }

        $first = $card->card_first_six ?? '******';
        $last  = $card->card_last_four ?? '****';

        return $first . ' **** ' . $last;
    }
}
