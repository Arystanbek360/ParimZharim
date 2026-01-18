<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Lednerb\ActionButtonSelector\ShowAsButton;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ForgetCachePermissionAndReloadOctane;

class ForgetCachePermissionAndReloadOctaneAdminAction extends BaseAdminAction
{

    use ShowAsButton;
    /**
     * @throws BindingResolutionException
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        ForgetCachePermissionAndReloadOctane::make()->handle();
    }

    public function name(): string
    {
        return 'Перезагрузить систему и сброс кеша прав доступа';
    }
}
