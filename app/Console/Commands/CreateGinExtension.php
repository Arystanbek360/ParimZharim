<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Core\Adapters\Cli\BaseCommand;
use Throwable;

class CreateGinExtension extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devcraft-web-platform:create-gin-extension {--connection=pgsql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Устанавливает разрешения pg_trgm для расширения gin в базе данных в схему public.';


    public function handle(): void
    {
        try {
            DB::connection($this->option('connection'))->statement('CREATE EXTENSION IF NOT EXISTS pg_trgm SCHEMA public;');
            $this->info('Extension created or already exists.');
        } catch (Throwable $e) {
            $this->error('Failed to create extension: ' . $e->getMessage());
        }
    }


}
