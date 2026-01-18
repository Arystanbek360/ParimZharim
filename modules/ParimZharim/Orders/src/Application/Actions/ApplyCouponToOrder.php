<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\LoyaltyProgram\Domain\Models\Coupon;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\CouponType;
use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\Shared\Core\Application\BaseAction;

class ApplyCouponToOrder extends BaseAction
{
    public function handle(int $orderID, int $couponAmount): void
    {

        $order = QueryOrderByID::make()->handle($orderID);

        $coupon = new Coupon();
        $coupon->amount = $couponAmount;
        $coupon->creator_id = $order->creator_id;
        $coupon->type = CouponType::PERCENT;
        $coupon->save();


        OrderService::applyCouponForOrder($order, $coupon);
    }
}
