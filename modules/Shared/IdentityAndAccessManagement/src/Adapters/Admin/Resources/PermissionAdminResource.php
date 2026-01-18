<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;

class PermissionAdminResource extends BaseAdminResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Permission>
     */
    public static string $model = Permission::class;


    /**
     * The model display name
     */
    public static function label(): string
    {
        return 'Разрешения';
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'label';

    public static function searchable(): bool
    {
        return false;
    }


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
            Text::make('Название', 'label'),
            Text::make('Описание', 'description')
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
        return [];
    }
}
