<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\Shared\Core\Adapters\Admin\Filters\BaseAdminDateFilter;

class OrderDateFilter extends BaseAdminDateFilter
{
    public $name = 'Дата заказа';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $selectedDate = Carbon::createFromFormat('Y-m-d', $value);

        return $query->where(function ($query) use ($selectedDate) {
            $query->whereDate('start_time', '<=', $selectedDate)
                ->whereDate('end_time', '>=', $selectedDate);
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        // Фильтр не использует дополнительные опции.
        return [];
    }
}
