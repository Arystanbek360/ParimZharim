<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Actions\ForgetCachePermissionAndReloadOctaneAdminAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ForgetCachePermissionAndReloadOctane;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;

class RoleAdminResource extends BaseAdminResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Role>
     */
    public static string $model = Role::class;


    /**
     * The model display name
     */
    public static function label(): string
    {
        return 'Роли';
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'label';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->creationRules('unique:idm_roles,name')
                ->updateRules('unique:idm_roles,name,{{resourceId}}')
                ->displayUsing(function ($value) {
                    return $this->label;
                }),

            BelongsToMany::make('Разрешения', 'permissions', PermissionAdminResource::class)
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return [
            (new ForgetCachePermissionAndReloadOctaneAdminAction())
            ->standalone()
                ->showAsButton()
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('reloadSystem', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('reloadSystem', $resource);
                })->confirmText('Вы уверены, что хотите перезагрузить систему и сбросить кеш прав доступа?')
                ->confirmButtonText('Да')
                ->size('md')
        ];
    }
}
