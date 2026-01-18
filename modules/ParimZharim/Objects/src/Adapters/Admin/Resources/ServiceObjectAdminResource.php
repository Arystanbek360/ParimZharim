<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Adapters\Admin\Resources;

use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class ServiceObjectAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = ServiceObject::class;

    public static $title = 'name';

    public static $search = [
        'name'
    ];

    public static function label(): string
    {
        return 'Объект';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(),
            Text::make('Наименование Объекта', 'name')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            Textarea::make('Описание объекта', 'description')->hideFromIndex()
                ->rules('required', 'max:255'),
            BelongsTo::make('Категория', 'category', CategoryAdminResource::class)
            ->showCreateRelationButton(),
            Number::make('Вместимость', 'capacity')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            //is_active
            Boolean::make('Активен?', 'is_active')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            Images::make('Главное изображение', 'main') // second parameter is the media collection name
            ->conversionOnIndexView('thumb') // conversion used to display the image
            ->rules('required', 'max:1024')
                ->setMaxFileSize(1024)
                ->withMeta(['value' => 'max. size 1MB'])
            ->setFileName(function($originalFilename, $extension, $model) {
                return 'main-image-'.uniqid().'.'.$extension;
            }),
            Images::make('Галерея', 'gallery')
                ->hideFromIndex()
                ->setMaxFileSize(1024)
                ->withMeta(['value' => 'max. size 1MB'])
            ->conversionOnDetailView('thumb') // conversion used on the model's view
            ->conversionOnForm('thumb') // conversion used to display the image on the model's form
            ->fullSize(),

            Tag::make('Теги', 'tags', TagAdminResource::class)
                ->showCreateRelationButton(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
