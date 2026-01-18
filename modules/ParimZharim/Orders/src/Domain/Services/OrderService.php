<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Services;

use Modules\ParimZharim\LoyaltyProgram\Domain\Models\Coupon;
use Modules\ParimZharim\Orders\Domain\Errors\InvalidOrderParams;
use Modules\ParimZharim\Orders\Domain\Errors\PlanNotFound;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemType;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Domain\BaseDomainService;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

class OrderService extends BaseDomainService
{

    public static function getOrderRepository(): OrderRepository
    {
        return app(OrderRepository::class);
    }

    /**
     * @throws InvalidOrderParams
     */
    public static function writeInitialOrderData(Order $order): void
    {
        $order->status = OrderStatus::CREATED;
        $order->creator_id = auth()->user()?->id;
        self::setConfirmBefore($order);
        self::markOrderAsNotSyncedInExternalSystem($order);
        self::calculateAndWriteOrderDiscountToMetadata($order);
    }

    /**
     * @throws InvalidOrderParams
     */
    public static function validateOrderParams(Order $order): void
    {
        self::checkForCorrectOrderTimeSlotParams($order);
    }

    public static function createOrderableServiceObjectOrderItemIfNotExist(Order $order): void
    {
        $orderRepository = self::getOrderRepository();
        $orderRepository->createOrderableServiceObjectOrderItemIfNotExist($order);
    }

    /**
     * @throws \Throwable
     * @throws InvalidOrderParams
     * @throws PlanNotFound
     */
    public static function performRequiredCalculationsOnOrderDataUpdate(Order $order): void
    {
        // Проверяем, что заказ не был синхронизирован во внешней системе
        if (!isset($order->metadata['is_synced_in_external_system'])) {
            self::markOrderAsNotSyncedInExternalSystem($order);
        }
        // Если скидка не была вычислена - вычисляем
        if (!isset($order->metadata['order_discount'])) {
            self::calculateAndWriteOrderDiscountToMetadata($order);
        }

        // Если изменился покупатель - пересчитываем скидку
        if ($order->isDirty(['customer_id'])) {
            self::calculateAndWriteOrderDiscountToMetadata($order);
        }

        // Устанавливаем новое время подтверждения
        self::setConfirmBefore($order);

        // Если время заказа изменилось - проверяем, что оно валидно
        if ($order->isDirty(['start_time', 'end_time', 'orderable_service_object_id'])) {
            self::checkForCorrectOrderTimeSlotParams($order);
        }

        //Всегда пересчитываем позицию заказа для объекта
        self::recalculateOrderableServiceObjectOrderItem($order);
    }

    public static function recalculateOrder(Order $order): void
    {
        $orderRepository = self::getOrderRepository();
        self::markOrderAsNotSyncedInExternalSystem($order);
        self::calculateAndWriteOrderDiscountToMetadata($order);
        self::recalculateOrderableServiceObjectOrderItem($order);
        self::recalculateProductsAndServicesOrderItems($order);
        $orderRepository->saveOrderQuietly($order);
    }


    public static function applyCouponForOrder(Order $order, ?Coupon $coupon = null): void
    {
        $orderRepository = self::getOrderRepository();
        $coupon = $coupon ?? $order->coupon; // Используем переданный купон, если он есть, иначе берем из заказа
        // Проведите расчёты, используя переданный купон
        $order->coupon = $coupon;
        self::calculateAndWriteOrderDiscountToMetadata($order);
        self::recalculateOrderableServiceObjectOrderItem($order);
        $orderRepository->saveOrderQuietly($order);
    }

    public static function calculateOrderItemsPrice(OrderItem $orderItem): void
    {
        $orderItem->price = $orderItem->calculatePrice();
        $orderItem->total = $orderItem->calculateTotal();
        $orderItem->total_with_discount = $orderItem->calculateTotalWithDiscount();
    }

    public static function getActualAdvancePayment(Order $order): float
    {
        $successfulPayment = $order->payments()->get()->filter(fn($payment) => $payment->status === PaymentStatus::SUCCESS || $payment->status === PaymentStatus::COMPLETED)->first();
        if ($successfulPayment) {
            return (float) $successfulPayment->total;
        }
        return 0;
    }

    private static function recalculateProductsAndServicesOrderItems(Order $order): void
    {
        $orderRepository = self::getOrderRepository();
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->type != OrderItemType::SERVICE_OBJECT) {
                self::calculateOrderItemsPrice($orderItem);
                $orderRepository->saveOrderQuietly($order);
            }
        }
    }

    private static function recalculateOrderableServiceObjectOrderItem(Order $order): void
    {
        $orderRepository = self::getOrderRepository();
        $orderItems = $order->orderItems;
        foreach ($orderItems as $orderItem) {
            if ($orderItem->type == OrderItemType::SERVICE_OBJECT) {
                $orderItem->setRelation('order', $order);
                self::calculateOrderItemsPrice($orderItem);
                $maxOrderItemHourPrice = $orderItem->calculateAdvancePayment();
                $metadata = $order->metadata;
                $metadata['expectedAdvancePayment'] = $maxOrderItemHourPrice;
                $order->metadata = $metadata;
                $orderRepository->saveOrderQuietly($order);
            }
        }

    }

    private static function markOrderAsNotSyncedInExternalSystem(Order $order): void
    {
        $metadata = $order->metadata;
        $metadata['is_synced_in_external_system'] = false;
        $order->metadata = $metadata;
    }

    private static function calculateAndWriteOrderDiscountToMetadata(Order $order, ?Coupon $coupon = null): void
    {
        $metadata = $order->metadata;
        $coupon = $coupon ?? $order->coupon; // Проверяем передан ли купон, если нет, берем из заказа
        $discountData = OrderableServiceObjectPriceCalculationService::getDiscountData(
            $order->orderableServiceObject,
            $order->start_time,
            $order->customer,
            $coupon
        );
        $metadata['order_discount'] = $discountData['discount'];
        $metadata['discount_reason'] = $discountData['reason'];
        $order->metadata = $metadata;
    }


    /**
     * @throws InvalidOrderParams
     */
    private static function setConfirmBefore(Order $order): void
    {
        $slotRules = OrderableObjectSlotsCalculatorService::getSlotRulesForServiceObjectOnDateAndTime($order->orderable_service_object_id, $order->start_time);
        if (!$slotRules) {
            throw new InvalidOrderParams('Не удалось найти правила расписания для объекта');
        }
        $order->confirm_before = now()->addMinutes((int)$slotRules['confirmation_waiting_duration']);
    }

    private static function checkForCorrectOrderTimeSlotParams(Order $order): void
    {
        if ($order->start_time > $order->end_time) {
            throw new InvalidOrderParams('Дата начала заказа не может быть позже даты окончания заказа');
        }

        $slotRules = OrderableObjectSlotsCalculatorService::getSlotRulesForServiceObjectOnDateAndTime($order->orderable_service_object_id, $order->start_time);
        if (!$slotRules) {
            throw new InvalidOrderParams('Не удалось найти правила расписания для объекта');
        }

        // Если заказ меньше минимальной длительности слота - ошибка
        if ($order->start_time->diffInMinutes($order->end_time) < (int)$slotRules['min_duration']) {
            throw new InvalidOrderParams('Заказ не может быть меньше минимальной длительности слота ' . $slotRules['min_duration'] . ' минут');
        }

        // Если заказ больше максимальной длительности слота - ошибка
        if ($order->start_time->diffInMinutes($order->end_time) > (int)$slotRules['max_duration']) {
            throw new InvalidOrderParams('Заказ не может быть больше максимальной длительности слота ' . $slotRules['max_duration'] . ' минут');
        }

        // Если заказ не попадает в рабочее время объекта - ошибка
        OrderableObjectSlotsCalculatorService::checkIfOrderCanBePlacedOnTimeOrFail($order->id, $order->orderable_service_object_id, $order->start_time, $order->end_time);
    }

    /**
     * @param Order $order
     * @return void
     */
    public static function writeDefaultOrderSource(Order $order): void
    {
        $metadata = $order->metadata ?? []; // Инициализируем metadata, если оно еще не установлено
        if (!isset($metadata['source'])) {
            $metadata['source'] = OrderSource::ADMIN_PANEL->value;
        }
        $order->metadata = $metadata;
    }

    public static function getCustomerTotalOrdersAmount(int $customerId): float
    {
        $orders = self::getOrderRepository()->getCompletedOrdersByCustomerId($customerId);

        $totalOrdersAmount = 0;
        foreach ($orders as $order) {
            $totalOrdersAmount += $order->total_with_discount;
        }

        return $totalOrdersAmount;
    }
}
