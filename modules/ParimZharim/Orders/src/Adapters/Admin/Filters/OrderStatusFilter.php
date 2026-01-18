<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\Shared\Core\Adapters\Admin\Filters\BaseAdminBooleanFilter;

class OrderStatusFilter extends BaseAdminBooleanFilter
{
    public function apply(Request $request, $query, $value): Builder
    {
        // Filter only the statuses that are set to true.
        $filteredStatuses = array_filter($value, function ($enabled) {
            return $enabled === true;
        });

        // Map the labels back to their enum values.
        $statusesToFilter = array_map(function ($label) {
            return OrderStatus::fromLabel($label);
        }, array_keys($filteredStatuses));

        // Remove any null values which might have been added if the label did not match any case
        $statusesToFilter = array_filter($statusesToFilter, function ($status) {
            return !is_null($status);
        });

        if (!empty($statusesToFilter)) {
            return $query->whereIn('status', $statusesToFilter);
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return array_map(function ($status) {
            return $status->label();
        }, OrderStatus::cases());
    }


    /**
     * Set the name of the filter to be displayed in the Nova dashboard.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Статус заказа';
    }
}
