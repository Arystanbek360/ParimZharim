<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Adapters\Admin\Resources;

use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;
use Modules\ParimZharim\Profile\Adapters\Admin\Resources\CustomerAdminResource;

class LoyaltyProgramCustomerAdminResource extends CustomerAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = LoyaltyProgramCustomer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     * @param NovaRequest $request
     */
    public function fields(NovaRequest $request):array
    {
        $fields = parent::fields($request);

        $fields[] = Number::make('Скидка', 'discount')
            ->min(0)
            ->default(function () {
                return 0;
            })
            ->max(100)
            ->step(1)
            ->sortable()
            ->displayUsing(fn($value) => $value . '%' )
            ->rules('nullable', 'numeric', 'min:0', 'max:100');

        return $fields;
    }

    public static function label(): string
    {
        return 'Участник программы лояльности';
    }
}
