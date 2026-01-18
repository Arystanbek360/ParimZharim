<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Application\Actions\ApplyCouponToOrder;
use Modules\ParimZharim\Orders\Application\Actions\CancelOrder;
use Modules\ParimZharim\Orders\Application\Actions\RecalculateOrder;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;

class RecalculateOrderAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            try {

                // Handle the confirmation logic
                RecalculateOrder::make()->handle($model->id);

                // Mark the model as finished
                $this->markAsFinished($model);
            } catch (OrderNotFound $e) {
                $this->markAsFailed($model, $e->getMessage());
            }

        }
    }

    public function name(): string
    {
        return 'Пересчитать заказ';
    }

}
