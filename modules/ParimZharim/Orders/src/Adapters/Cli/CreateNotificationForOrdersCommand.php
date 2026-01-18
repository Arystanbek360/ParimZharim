<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Cli;

use Modules\ParimZharim\Orders\Application\Actions\CreateNotificationForOrders;
use Modules\Shared\Core\Adapters\Cli\BaseCommand;

class CreateNotificationForOrdersCommand extends BaseCommand
{
    protected $signature = 'orders:create-notification';
    protected $description = 'Создает уведомления для заказов';

    public function handle(): void
    {
       CreateNotificationForOrders::make()->handle();
    }
}
