<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Core\Adapters\Cli\BaseCommand;
use Throwable;

class CreateTestDatabase extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devcraft-web-platform:create-test-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создаёт тестовую базу данных laravel_test';


    public function handle(): void
    {
        try {
            DB::statement('DROP DATABASE IF EXISTS laravel_test;');
            DB::statement("CREATE DATABASE laravel_test OWNER = sail ENCODING = 'UTF8' TABLESPACE = pg_default;");
            $this->info('Test database is created.');
        } catch (Throwable $e) {
            $this->error('Failed to create database: ' . $e->getMessage());
        }
    }


}
