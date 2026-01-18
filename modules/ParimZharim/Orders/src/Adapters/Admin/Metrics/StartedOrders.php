<?php declare(strict_types=1);


namespace Modules\ParimZharim\Orders\Adapters\Admin\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;

class StartedOrders extends Value
{

    public function name(): string
    {
        return 'Начатые заказы';
    }
    public function calculate(NovaRequest $request): ValueResult
    {
        $count =  Order::where('status', OrderStatus::STARTED)->count();
        return $this->result($count);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'started';
    }
}
