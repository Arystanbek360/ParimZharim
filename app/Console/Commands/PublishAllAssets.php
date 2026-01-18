<?php declare(strict_types=1);

namespace App\Console\Commands;

use Modules\Shared\Core\Adapters\Cli\BaseCommand;

class PublishAllAssets extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devcraft-web-platform:publish-all-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Публикует все ресурсы всех модулей + Livewire';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'livewire:assets',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'cms-module-assets',
            '--force' => true,
        ]);
    }
}
