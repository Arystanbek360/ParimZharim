<?php declare(strict_types=1);


namespace Modules\ParimZharim\Orders\Adapters\Admin\Metrics;




use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Modules\ParimZharim\Orders\Domain\Models\Order;

class TotalOrders extends Value
{

    public function name(): string
    {
        return 'Всего заказов';
    }
    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function calculate(NovaRequest $request): ValueResult
    {
        $count = Order::count();  // Получаем количество заказов без фильтрации
        return $this->result($count);  // Просто возвращаем результат без сравнения
    }




    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'total';
    }
}
