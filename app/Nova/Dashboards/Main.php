<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboards\Main as Dashboard;
use Modules\ParimZharim\Orders\Adapters\Admin\Metrics\ActiveOrders;
use Modules\ParimZharim\Orders\Adapters\Admin\Metrics\StartedOrders;
use Modules\ParimZharim\Orders\Adapters\Admin\Metrics\TotalOrders;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new TotalOrders,
            new ActiveOrders,
            new StartedOrders,

        ];
    }

    public function label(): string
    {
        return 'Статистика';
    }

    public function name(): string
    {
        return 'Статистика';
    }
}
