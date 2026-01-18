<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Adapters\Admin\Filters\BaseAdminDateFilter;

class ObjectDateFilter extends BaseAdminDateFilter
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
        // Фактически фильтр не применяется, используется только для передачи данных и обработки в OrderableObjectSlotsTableAdminResource
        $selectedDate = Carbon::createFromFormat('Y-m-d', $value);
        $request->request->add(['order_date' => $selectedDate]); // Добавление выбранной даты в параметры запроса

        return $query;
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
