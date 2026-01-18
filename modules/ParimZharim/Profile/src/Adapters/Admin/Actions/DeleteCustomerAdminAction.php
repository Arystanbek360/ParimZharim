<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Modules\ParimZharim\Profile\Application\Actions\DeleteCustomer;
use Modules\ParimZharim\Profile\Domain\Errors\CustomerNotFound;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Throwable;

class DeleteCustomerAdminAction extends BaseAdminAction
{

    /**
     * Perform the action on the selected models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        $errors = [];
        foreach ($models as $model) {
            try {
                // Assuming $model is an instance of Customer
              DeleteCustomer::make()->handle($model->id);
            } catch (CustomerNotFound $e) {
                // Collect error messages instead of returning
                $errors[] = "Customer ID {$model->id} not found.";
                continue; // Continue processing other models
            } catch (Throwable $e) {
                // Collect error messages instead of returning
                $errors[] = "Error for customer ID {$model->id}: " . $e->getMessage();
                continue; // Continue processing other models
            }
        }

        $request = request();  // This is equivalent to injecting NovaRequest if the context allows
        if (!empty($errors)) {
            $request->session()->flash('danger', implode(' ', $errors));
        } else {
            $request->session()->flash('success', 'Данные клиента были удалены');
        }
    }

    /**
     * Return the action name as it should appear in the UI
     *
     * @return string
     */
    public function name(): string
    {
        return 'Удалить данные клиента';
    }

}
