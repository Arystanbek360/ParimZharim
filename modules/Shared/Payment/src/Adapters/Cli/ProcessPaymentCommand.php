<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Cli;

use Modules\Shared\Core\Adapters\Cli\BaseCommand;
use Modules\Shared\Payment\Application\Actions\ProceedPaymentLifecycle;

class ProcessPaymentCommand extends BaseCommand
{
    protected $signature = 'payment:process';

    public function handle(): void
    {
        ProceedPaymentLifecycle::make()->handle();
    }
}
