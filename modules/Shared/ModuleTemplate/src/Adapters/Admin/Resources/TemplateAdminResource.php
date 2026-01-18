<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Adapters\Admin\Resources;

use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Sparkline;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\UiAvatar;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Locale;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\ModuleTemplate\Domain\Models\Template;

class TemplateAdminResource extends BaseAdminResource
{
    public static string $model = Template::class;

    public static $title = 'Черновик'; // Setting the title as "Draft"

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Panel::make('Личная информация', [ // Translated to Russian
                Text::make('Имя', 'name')->rules('required', 'max:255'),
                UiAvatar::make(),
                Textarea::make('Описание', 'description')->nullable(),
                Trix::make('Текст для отображения', 'display_text')->nullable()
                    ->hideFromIndex(),
                Number::make('Номер', 'number'),
                Select::make('Тип', 'type')->options([
                    'TEMPLATE' => 'Шаблон',
                ])->displayUsingLabels()->filterable(),
                DateTime::make('Дата', 'date')
                    ->sortable(),
                Boolean::make('Активный', 'active'),
                Badge::make('Статус', 'status')->map([
                    'active' => 'success',
                    'inactive' => 'danger',
                ]),
                Status::make('Статус', 'status')
                    ->loadingWhen(['active'])
                    ->failedWhen(['inactive'])
                    ->hideWhenCreating()
                    ->hideWhenUpdating(),
                Sparkline::make('Просмотры поста', 'post_views')
                    ->data([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
                    ->asBarChart()
                    ->height(200)
                    ->width(600)
                    ->hideWhenCreating()
                    ->hideFromIndex(),
                Email::make('Электронная почта', 'email')->rules('required'),
            ]),
            Panel::make('Дополнительная информация', [
                URL::make('Пример URL', 'url')
                    ->nullable()
                    ->hideFromIndex(),
                Currency::make('Цена', 'price')
                    ->step(0.01)
                    ->min(0)
                    ->max(999999.99)
                    ->currency('USD'),
                Code::make('Метаданные', 'metadata')->json()
                    ->hideFromIndex(),
                Country::make('Страна', 'country')
                    ->default('KZ')
                    ->displayUsing(function ($value) {
                        // Replace the placeholder with actual code to resolve country names
                        return Locale::getDisplayRegion('-' . $value, 'ru');
                    }),
                Password::make('Пароль', 'password')
                    ->creationRules('required', 'string', 'min:6')
                    ->updateRules('nullable', 'string', 'min:6')
                    ->hideFromIndex()
                    ->hideFromDetail(),
                Slug::make('Slug', 'slug')
                    ->hideFromIndex(),
            ]),
            Image::make('Фото', 'photo')->disableDownload(),
            Images::make('Главное изображение', 'main') // second parameter is the media collection name
            ->conversionOnIndexView('thumb') // conversion used to display the image
            ->rules('required')
                ->setFileName(function($originalFilename, $extension, $model) {
                    return 'main-image-'.uniqid().'.'.$extension;
                }),
            Images::make('Галерея', 'gallery')
                ->hideFromIndex()
                ->conversionOnDetailView('thumb') // conversion used on the model's view
                ->conversionOnForm('thumb') // conversion used to display the image on the model's form
                ->fullSize(),
            KeyValue::make('Данные шаблона', 'template_data')
                ->keyLabel('Контакт')
                ->valueLabel('Значение')
                ->actionText('Добавить информацию'),
            Markdown::make('Биография', 'biography'),
        ];
    }
}
