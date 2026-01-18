<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Application\DTO\OrderData;
use Modules\ParimZharim\Orders\Domain\Errors\OrderableObjectNotFound;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerById;
use Modules\ParimZharim\Profile\Domain\Errors\CustomerNotFound;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Application\NotifyAllAdmins;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;

class CreateOrder extends BaseAction
{

    public function __construct(
        private readonly OrderRepository $orderRepository
    ) {
    }

    public function handle(OrderData $data): Order
    {
        $orderCustomer = GetCustomerById::make()->handle($data->customerID);
        if ($orderCustomer === null) {
            throw new CustomerNotFound($data->customerID);
        }

        $orderableServiceObject = GetOrderableServiceObjectByID::make()->handle($data->serviceObjectID);
        if ($orderableServiceObject === null) {
            throw new OrderableObjectNotFound($data->serviceObjectID);
        }

        // TODO: строгая типизация
        $metadata = [
            'guests_adults' => $data->guestsAdults,
            'guests_children' => $data->guestsChildren,
            'customerNotes' => $data->customerNotes,
            'paymentMethod' => PaymentMethodType::from($data->paymentMethod),
            'source' => $data->source->value,
        ];

        //create order
        $order = new Order([
            'customer_id' => $orderCustomer->id,
            'orderable_service_object_id' => $orderableServiceObject->id,
            'start_time' => $data->timeFrom,
            'end_time' => $data->timeTo,
            'metadata' => $metadata
        ]);

        $this->orderRepository->saveOrder($order);

        //add notification of administrator about new order
        NotifyAllAdmins::make()->handle("Создан новый заказ, необходимо проверить и подтвердить его");

        return $order;
    }
}
