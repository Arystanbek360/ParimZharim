<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Errors\OrderableObjectNotFound;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\ParimZharim\Orders\Domain\Services\OrderableServiceObjectPriceCalculationService;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Application\BaseAction;

class CalculatePriceByDateAndObject extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    public function handle(int $orderableServiceObjectId, Customer $customer, Carbon $startDate, Carbon $endDate, int $guests): array
    {
        $orderableServiceObject = $this->orderableServiceObjectRepository->getOrderableServiceObjectById($orderableServiceObjectId);
        if (!$orderableServiceObject) {
            throw new OrderableObjectNotFound($orderableServiceObjectId);
        }

        $data = OrderableServiceObjectPriceCalculationService::calculatePriceAndMetadata(
            $orderableServiceObject,
            $startDate,
            $endDate,
            $customer,
            $guests
        );

        $total_price = $data['totalObjectBookingPrice'];
        $discount = $data['discount'];
        $discountPrice = $data['discountPrice'];
        $total_price_with_discount = $total_price - $discountPrice;

        $advancePayment = $data['advancePayment'];

        return [
            'total_price' => $total_price,
            'discount_percent' => $discount,
            'discount_price' => $discountPrice,
            'total_price_with_discount' => $total_price_with_discount,
            'advance_payment' => $advancePayment,
        ];

    }
}
