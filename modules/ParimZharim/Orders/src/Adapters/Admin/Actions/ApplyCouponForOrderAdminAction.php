<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Application\Actions\ApplyCouponToOrder;
use Modules\ParimZharim\Orders\Application\Actions\CancelOrder;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;

class ApplyCouponForOrderAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            try {

                // Handle the confirmation logic
                ApplyCouponToOrder::make()->handle($model->id, (int)$fields->get('couponAmount'));

                // Mark the model as finished
                $this->markAsFinished($model);
            } catch (OrderNotFound $e) {
                $this->markAsFailed($model, $e->getMessage());
            }

        }
    }

    public function fields(NovaRequest $request): array
    {
        //amount of discount
        return [
            Number::make('Скидка %', 'couponAmount')
                ->min(0)
                ->max(100)
                ->step(1)
                ->required()
        ];
    }

    public function name(): string
    {
        return 'Применить купон';
    }

}
