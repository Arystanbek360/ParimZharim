<?php declare(strict_types=1);

namespace Modules\Shared\Core\Adapters\Cli;

use Illuminate\Console\Command;

/**
 * Class BaseCommand
 */
abstract class BaseCommand extends Command
{
    /**
     * Выполнение команды.
     *
     * @return void
     */
    abstract public function handle(): void;
}
