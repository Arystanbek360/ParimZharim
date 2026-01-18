<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Cli;

use Modules\ParimZharim\Orders\Application\Actions\ProceedOrderLifecycleAction;
use Modules\Shared\Core\Adapters\Cli\BaseCommand;

class ProceedOrderLifecycleConsoleCommand extends BaseCommand
{
    protected $signature = 'orders:proceed-lifecycle';

    public function handle(): void
    {
       ProceedOrderLifecycleAction::make()->handle();
    }
}
