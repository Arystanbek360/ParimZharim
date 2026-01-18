<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Core\Adapters\Cli\BaseCommand;

class Rollback extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devcraft-web-platform:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Откатывает миграции после проверки активного проекта';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Получить активный проект из app.project
        $currentProject = config('app.project');

        // Путь к файлу провайдеров
        $platformDirRoot = base_path();
        $providersFilePath = $platformDirRoot . '/bootstrap/providers.php';

        // Проверка наличия файла провайдеров
        if (!File::exists($providersFilePath)) {
            $this->error("Файл провайдеров не существует: $providersFilePath");
            Log::error('Файл провайдеров не существует.', ['path' => $providersFilePath]);
        }

        // Загрузка провайдеров из файла
        $providers = include $providersFilePath;

        // Проверка провайдеров
        foreach ($providers as $provider) {
            $provider = ltrim($provider, '\\'); // Удаление начального слэша
            if (
                !str_starts_with($provider, 'App\Providers') &&
                !str_starts_with($provider, 'Modules\Shared') &&
                !str_starts_with($provider, 'Modules\\' . $currentProject)
            ) {
                $this->error("Error: Обнаружен неавторизованный поставщик модулей: $provider");
            }
        }

        // Если все проверки пройдены, откатываем миграции
        $this->call('migrate:rollback', [
            '--force' => true,
        ]);

        Log::info('Откат миграций выполнен успешно.');

    }
}
