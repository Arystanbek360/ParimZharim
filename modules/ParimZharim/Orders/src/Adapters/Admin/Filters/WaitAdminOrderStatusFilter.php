<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;

class WaitAdminOrderStatusFilter extends OrderStatusFilter
{
    public function options(Request $request): array
    {
        // Define the statuses that are valid for active orders
        $activeStatuses = [
            OrderStatus::CREATED,
            OrderStatus::CANCELLATION_REQUESTED,
            OrderStatus::FINISHED
        ];

        // Retrieve the labels for only the active statuses
        return array_map(function ($status) {
            return $status->label();
        }, array_filter(OrderStatus::cases(), function ($status) use ($activeStatuses) {
            return in_array($status, $activeStatuses);
        }));
    }

}
