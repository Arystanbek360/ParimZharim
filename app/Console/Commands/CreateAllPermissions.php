<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Contracts\Container\BindingResolutionException;
use Modules\Shared\Core\Adapters\Cli\BaseCommand;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\EnumResolver as RolesAndPermissionsEnumResolver;
use Throwable;

class CreateAllPermissions extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devcraft-web-platform:create-all-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает все разрешения для всех модулей';

    /**
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $enumResolver = app()->make(RolesAndPermissionsEnumResolver::ENUM_RESOLVER_CONTAINER);

        foreach ($enumResolver as $enumClass) {
            $permissions = $enumClass::cases(); // Получаем все случаи Enum
            $this->createPermissionsFromEnum($permissions);
        }

        $this->info('Все разрешения успешно созданы.');
    }

    protected function createPermissionsFromEnum($permissions): void
    {
        foreach ($permissions as $permission) {
            try {
                Permission::firstOrCreate(['name' => $permission->value]);
                $this->info("Разрешение {$permission->value} создано или уже существует.");
            } catch (Throwable $e) {
                $this->error("Ошибка при создании разрешения {$permission->value}: " . $e->getMessage());
            }
        }
    }
}
