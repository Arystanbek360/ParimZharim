<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class ContentAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = Content::class;

    /**
     * The single value that should be used to represent the resource in the UI.
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id', 'title', 'slug', 'content'
    ];

    /**
     * The model display name
     */
    public static function label(): string
    {
        return 'Контент';
    }

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel(): ?string
    {
        return 'Создать контент';
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

            Text::make('Заголовок', 'title')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Slug', 'slug')
                ->sortable()
                ->creationRules('required', 'unique:cms_contents,slug', 'max:255')
                ->updateRules('required', 'unique:cms_contents,slug,{{resourceId}}', 'max:255'),

            BelongsTo::make('Категория', 'category', ContentCategoryAdminResource::class)
                ->sortable()
                ->showCreateRelationButton()
                ->nullable(),

            Trix::make('Контент', 'content')
                ->sortable()
                ->rules('required')
                 ->withFiles('public'),

            DateTime::make('Создано', 'created_at')
                ->onlyOnDetail()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            DateTime::make('Обновлено', 'updated_at')
                ->onlyOnDetail()
                ->hideWhenCreating()
                ->hideWhenUpdating(),
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
