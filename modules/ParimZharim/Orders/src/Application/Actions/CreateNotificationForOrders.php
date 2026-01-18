<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Application\CreateNotification;

class CreateNotificationForOrders extends BaseAction
{
    public function __construct(
        private readonly OrderRepository $orderRepository
    ){}

    public function handle(): void
    {
        $orders = $this->orderRepository->getOrdersToNotify();

        /** @var Order $order */
        foreach ($orders as $order) {
            $user = $order->customer->user;
            $title = "Напоминание о вашем заказе";
            $message = $this->generateMessageText($order);
            $metadata = [
                'order_id' => $order->id,
                'phone' => $order->customer->phone,
            ];
            $type = "order_reminder";
            CreateNotification::make()->handle($user, $title, $message, $type, $metadata);
        }
    }

    private function generateMessageText(Order $order): string
    {
        $name = $order->customer->name;
        $object = $order->orderableServiceObject->name;

        // Получаем тайм-зону объекта
        $timezone = $order->orderableServiceObject->getObjectTimezone();

        // Преобразуем время начала заказа из UTC в тайм-зону объекта
        $startTimeUtc = $order->start_time; // Время в UTC из базы данных
        $startTime = $startTimeUtc->copy()->setTimezone($timezone);

        // Устанавливаем локаль для Carbon
        Carbon::setLocale('ru');

        // Форматируем дату и время для сообщения
        $date = $startTime->format('d.m.Y'); // Форматирует дату как дд.мм.гггг
        $time = $startTime->format('H:i');

        // Формируем сообщение без экранирования, используя одинарные кавычки
        $message = 'Добрый день, ' . $name . '! Банный комплекс "Парим/Жарим" напоминает Вам, что завтра ' . $date . ' в ' . $time . ' у Вас забронирован(-а) ' . $object . '. Ждём Вас!';

        return $message;
    }
}
