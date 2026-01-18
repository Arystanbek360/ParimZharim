<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\CMS\Domain\Models\ContentCategory;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class ContentCategoryAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = ContentCategory::class;

    /**
     * The single value that should be used to represent the resource in the UI.
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id', 'title'
    ];

    /**
     * The model display name
     */
    public static function label(): string
    {
        return 'Категория контента';
    }

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel(): ?string
    {
        return 'Создать категорию контента';
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

            Text::make('Заголовок', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Textarea::make('Описание', 'description')
                ->sortable()
                ->alwaysShow()
                ->rules('nullable'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
