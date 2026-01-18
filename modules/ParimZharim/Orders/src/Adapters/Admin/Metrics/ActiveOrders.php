<?php declare(strict_types=1);


namespace Modules\ParimZharim\Orders\Adapters\Admin\Metrics;


use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;

class ActiveOrders extends Value
{

    public function name(): string
    {
        return 'Активные заказы';
    }
    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function calculate(NovaRequest $request): ValueResult
    {
        $count = Order::whereNotIn('status', [
            OrderStatus::CREATED,
            OrderStatus::CANCELLED,
            OrderStatus::COMPLETED,
            OrderStatus::CANCELLATION_REQUESTED,
            OrderStatus::FINISHED
        ])
            //where start time is today
            ->where('start_time', '>=', Carbon::now()->startOfDay())
            ->where('start_time', '<=', Carbon::now()->endOfDay())
            ->count();

        return $this->result($count);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey(): string
    {
        return 'active';
    }
}
