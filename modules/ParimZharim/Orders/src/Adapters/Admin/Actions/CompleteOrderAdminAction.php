<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Modules\ParimZharim\Orders\Application\Actions\CompleteOrder;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;

class CompleteOrderAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
             try {
                 CompleteOrder::make()->handle($model->id);
                 $this->markAsFinished($model);
             } catch (OrderNotFound|StatusChangeViolation $e) {
                 $this->markAsFailed($model, $e->getMessage());
             }
        }
    }

    public function name(): string
    {
        return 'Подтвердить выполнение заказа';
    }
}
