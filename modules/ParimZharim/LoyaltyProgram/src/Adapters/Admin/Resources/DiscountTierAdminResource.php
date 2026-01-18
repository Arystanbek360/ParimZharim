<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Adapters\Admin\Resources;

use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTier;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class DiscountTierAdminResource extends BaseAdminResource
{

    public static string $model = DiscountTier::class;

    //on russian
    public static function label(): string
    {
        return 'Управление скидками';
    }

    //on russian
    public static function singularLabel(): string
    {
        return 'Управление скидками';
    }

    public static function createButtonLabel(): string
    {
        return 'Добавить скидку';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Number::make('Процент скидки', 'discount_percentage')
                ->rules('required', 'numeric', 'min:0', 'max:100') // Поле для процента скидки
                ->displayUsing(function ($value) {
                    return $value . '%';
                })
                ->sortable()
                ->textAlign('left')
                ->help('Введите процент скидки от 0 до 100'),

            Currency::make('Минимальная сумма', 'threshold_amount')
                ->rules('required', 'numeric', 'min:0')
                ->currency('KZT')
                ->textAlign('left')
                ->sortable()
                ->help('Введите минимальную сумму для применения скидки'),

            Date::make('Действует с', 'start_date')
                ->required()
                ->sortable()
                ->help('Укажите дату начала действия скидки'),
        ];
    }


}
