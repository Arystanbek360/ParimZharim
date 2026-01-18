<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Api\ApiControllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Adapters\Api\Transformers\DetailOrderTransformer;
use Modules\ParimZharim\Orders\Adapters\Api\Transformers\OrderForListTransformer;
use Modules\ParimZharim\Orders\Application\Actions\AddOrderItem;
use Modules\ParimZharim\Orders\Application\Actions\CalculatePriceByDateAndObject;
use Modules\ParimZharim\Orders\Application\Actions\CreateAdvancePaymentForOrder;
use Modules\ParimZharim\Orders\Application\Actions\CreateOrder;
use Modules\ParimZharim\Orders\Application\Actions\GetScheduleDetailsForObjectOnDate;
use Modules\ParimZharim\Orders\Application\Actions\QueryActiveOrderForCustomer;
use Modules\ParimZharim\Orders\Application\Actions\QueryOrderByID;
use Modules\ParimZharim\Orders\Application\Actions\QueryOrdersByCustomer;
use Modules\ParimZharim\Orders\Application\Actions\RequestOrderCancellation;
use Modules\ParimZharim\Orders\Application\ApplicationError\AdvancePaymentIsAlreadyCreated;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Application\DTO\OrderData;
use Modules\ParimZharim\Orders\Application\DTO\OrderItemData;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;
use Modules\ParimZharim\Orders\Domain\Errors\OrderableObjectNotFound;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerByUser;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Throwable;

class OrderApiController extends BaseApiController
{


    public function createOrderByCustomer(Request $request): JsonResponse
    {
        $objectId = (int)$request->input('object_id');
        $bookingDate = $request->input('date.booking_date');
        $checkIn = $request->input('date.check_in');
        $hours = $request->input('date.hours');
        $adults = (int)$request->input('persons.adults');
        $kids = (int)$request->input('persons.kids');
        $customerNotes = $request->input('notes');
        $paymentMethod = $request->input('payment_method');

        if (!$objectId || !$bookingDate || !$checkIn || $hours === null || $adults === null) {
            return $this->respondError('Missing required fields', 400);
        }

        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }
        $customerID = $customer->id;

        //time comes in Asia/Almaty timezone, need to convert it to UTC
        $timeFrom = $bookingDate . ' ' . $checkIn;
        $timeFromInUTC = Carbon::parse($timeFrom, 'Asia/Almaty')->setTimezone('UTC');

        //set timeTo UTC time
        $timeTo = date('Y-m-d H:i:s', strtotime($timeFrom . ' + ' . $hours . ' hours'));
        $timeToInUTC = Carbon::parse($timeTo, 'Asia/Almaty')->setTimezone('UTC');

        $orderData = new OrderData(
            serviceObjectID: $objectId,
            customerID: $customerID,
            guestsAdults: $adults,
            guestsChildren: $kids,
            timeFrom: $timeFromInUTC->format('Y-m-d H:i:s'),
            timeTo: $timeToInUTC->format('Y-m-d H:i:s'),
            customerNotes: $customerNotes ?? '',
            paymentMethod: $paymentMethod,
            source: OrderSource::MOBILE_APP,
        );

        $order = CreateOrder::make()->handle($orderData);

        $products = $request->input('products', []);
        $services = $request->input('additional_services', []);

        $orderItems = [];
        foreach ($products as $product) {
            $orderItems[] = new OrderItemData(
                orderableID: (int)$product['product_id'],
                quantity: (int)$product['product_quantity'],
                type: 'product'
            );
        }

        foreach ($services as $service) {
            $orderItems[] = new OrderItemData(
                orderableID: (int)$service['service_id'],
                quantity: (int)$service['quantity'],
                type: 'service'
            );
        }

        AddOrderItem::make()->handle($order->id, $orderItems);

        $data = ['order_id' => $order->id];
        return $this->respond($data);
    }

    public function requestUpdatingOrder(Request $request): JsonResponse
    {
        $orderID = (int)$request->input('order_id');
        $products = $request->input('products', []);
        $services = $request->input('additional_services', []);

        if (!$orderID) {
            return $this->respondError('Missing required fields', 400);
        }

        try {
            $order = QueryOrderByID::make()->handle($orderID);
        } catch (OrderNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        }

        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }
        $customerID = $customer->id;
        if ($order->customer_id !== $customerID) {
            return $this->respondError('Order does not belong to this customer', 403);
        }

        $orderItems = [];

        foreach ($products as $product) {
            $orderItems[] = new OrderItemData(
                orderableID: (int)$product['product_id'],
                quantity: (int)$product['product_quantity'],
                type: 'product'
            );
        }

        foreach ($services as $service) {
            $orderItems[] = new OrderItemData(
                orderableID: (int)$service['service_id'],
                quantity: (int)$service['quantity'],
                type: 'service'
            );
        }

        AddOrderItem::make()->handle($orderID, $orderItems);


        return $this->respondSuccess();
    }


    /**
     * @throws OrderNotFound
     */
    public function requestOrderCancellation(Request $request): JsonResponse
    {
        // Retrieve the customer associated with the currently authenticated user
        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }

        // Get the customer ID
        $customerID = $customer->id;

        // Assuming the request contains the order ID
        $orderID = (int)$request->input('order_id');
        if (!$orderID) {
            return $this->respondError('Order ID is required', 400);
        }

        try {
            $order = QueryOrderByID::make()->handle($orderID);
        } catch (OrderNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        }

        if (!$order) {
            return $this->respondError((new OrderNotFound($orderID))->getMessage(), 404);
        }

        if ($order->customer_id !== $customerID) {
            return $this->respondError('Order does not belong to this customer', 403);
        }

        try {
            RequestOrderCancellation::make()->handle($orderID);
        } catch (OrderNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        } catch (StatusChangeViolation $e) {
            return $this->respondError($e->getMessage(), 409);
        }

        return $this->respondSuccess();
    }

    public function viewOrderDetailsByCustomer(Request $request): JsonResponse
    {
        // Retrieve the customer associated with the currently authenticated user
        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }

        // Get the customer ID
        $customerID = $customer->id;

        // Assuming the request contains the order ID
        $orderID = (int)$request->input('order_id');
        if (!$orderID) {
            return $this->respondError('Order ID is required', 400);
        }

        // Retrieve the order if it belongs to the customer
        $order = QueryOrderByID::make()->handle($orderID);

        if ($order->customer_id !== $customerID) {
            return $this->respondError('Order does not belong to this customer', 404);
        }

        $transformer = new DetailOrderTransformer();
        $data = $transformer->transform($order);

        return $this->respond($data);
    }


    public function getOrdersByCustomer(Request $request): JsonResponse
    {
        // Assume user method on request returns user object linked with customer ID
        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }
        $customerID = $customer->id;

        try {
            $orderCollection = QueryOrdersByCustomer::make()->handle($customerID);
            if ($orderCollection->isEmpty()) {
                return $this->respond($orderCollection->all());
            }

            $transformer = new OrderForListTransformer();
            $data = $orderCollection->map(fn($order) => $transformer->transform($order))->all();

            return $this->respond($data);
        } catch (Throwable $e) {
            return $this->respondError('Failed to retrieve orders: ' . $e->getMessage(), 500);
        }
    }


    public function getActiveOrder(Request $request): JsonResponse
    {
        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }
        $customerID = $customer->id;

        if (!$customerID) {
            return $this->respondError('No customer ID associated with user', 400);
        }

        try {
            $activeOrder = QueryActiveOrderForCustomer::make()->handle($customerID);
            if ($activeOrder) {
                $data = [
                    'active_order' => $activeOrder->id,
                ];
            } else {
                $data = [
                    'active_order' => null,
                ];
            }
            return $this->respond($data);
        } catch (Throwable $e) {
            return $this->respondError('Failed to retrieve active order: ' . $e->getMessage(), 500);
        }
    }

    public function preCalculatePriceByDateAndObject(Request $request): JsonResponse
    {
        $objectId = (int)$request->input('object_id');
        $bookingDate = $request->input('date.booking_date');
        $checkIn = $request->input('date.check_in');
        $hours = $request->input('date.hours');
        $adults = (int)$request->input('persons.adults');
        $kids = (int)$request->input('persons.kids');

        if (!$objectId || !$bookingDate || !$checkIn || $hours === null) {
            return $this->respondError('Missing required fields', 400);
        }

        $customer = GetCustomerByUser::make()->handle($request->user());
        if (!$customer) {
            return $this->respondError('No customer linked with this user', 400);
        }

        //time comes in Asia/Almaty timezone, need to convert it to UTC
        $timeFrom = $bookingDate . ' ' . $checkIn;
        $timeFromInUTC = Carbon::parse($timeFrom, 'Asia/Almaty')->setTimezone('UTC');

        //set timeTo UTC time
        $timeTo = date('Y-m-d H:i:s', strtotime($timeFrom . ' + ' . $hours . ' hours'));
        $timeToInUTC = Carbon::parse($timeTo, 'Asia/Almaty')->setTimezone('UTC');

        try {
            $data = CalculatePriceByDateAndObject::make()->handle($objectId, $customer, $timeFromInUTC, $timeToInUTC, $adults + $kids);
        } catch (OrderableObjectNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        }

        return $this->respond($data);
    }


    public function createPaymentForOrder(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer',
            'payment_method' => 'required|string',
        ]);

        $orderID = (int)$request->input('order_id');

        $paymentMethod = PaymentMethodType::from($request->input('payment_method'));

        $order = QueryOrderByID::make()->handle($orderID);

        $price = $order->metadata['expectedAdvancePayment'];

        try {
            $payment = CreateAdvancePaymentForOrder::make()->handle($orderID, $price, $paymentMethod);
            $paymentUrl = null;
            if ($paymentMethod == PaymentMethodType::CLOUD_PAYMENT) {
                $paymentUrl = config('app.url') . '/payment-widget/' . $payment->id;
            }

            return response()->json(['payment_id' => $payment->id,
                'payment_url' => $paymentUrl]);
        } catch (StatusChangeViolation|AdvancePaymentIsAlreadyCreated $e) {
            return $this->respondError($e->getMessage(), 409);
        }

    }
}
