<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\ParimZharim\Orders\Domain\Errors\InvalidOrderParams;
use Modules\ParimZharim\Orders\Domain\Errors\MoreThanOneScheduleOnDate;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Schedule;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\Shared\Core\Domain\BaseDomainService;

class OrderableObjectSlotsCalculatorService extends BaseDomainService
{

    public static function getOrderableServiceObjectRepository(): OrderableServiceObjectRepository
    {
        return app(OrderableServiceObjectRepository::class);
    }

    public static function getSlotsAndDurationForServiceObjectOnDate(int $serviceObjectID, Carbon $startDate, Carbon $endDate): array
    {
        $orderableServiceObjectRepository = self::getOrderableServiceObjectRepository();
        $slots = self::getSlotsForObjectOnDate($serviceObjectID, $startDate, $endDate, 60);

        // Get next day's slots for calculating max duration
        $nextDaySlots = self::getSlotsForObjectOnDate($serviceObjectID, $endDate->copy()->addDay(), $endDate->copy()->addDay(), 60);

        // Combine current day's slots with next day's slots for duration calculation
        $allSlots = array_merge($slots, $nextDaySlots);
        $object = $orderableServiceObjectRepository->getOrderableServiceObjectById($serviceObjectID);
        $schedules = $orderableServiceObjectRepository->loadSchedulesForDate($object, $startDate, $endDate);

        $filteredSlots = self::filterServiceDurationSlots($allSlots, $schedules, $startDate, $object);

        $result = [];

        // Проходим по всем слотам
        foreach ($filteredSlots as $slotKey => $slot) {
            // Если слот занят или нельзя начать бронирование, переходим к следующему слоту
            if (!$slot['is_free'] || !$slot['can_start_booking']) {
                continue;
            }

            // Получаем минимальную и максимальную длительность для слота
            list($minDuration, $maxDuration) = self::getDurationsFromSchedule($schedules, $startDate, $slot);

            $slotStart = Carbon::parse($slot['start']);
            $maxBookingTime = $maxDuration;

            // Find the maximum possible duration for the slot
            foreach ($filteredSlots as $nextSlotKey => $nextSlot) {
                if ($nextSlotKey <= $slotKey) {
                    continue;
                }

                $nextSlotStart = Carbon::parse($nextSlot['start']);
                if (!$nextSlot['is_free'] || $nextSlotStart->diffInMinutes($slotStart) > $maxDuration) {
                    $maxBookingTime = min($maxBookingTime, $slotStart->diffInMinutes($nextSlotStart));
                    break;
                }
            }

            $minDurationHours = floor($minDuration / 60);
            $maxDurationHours = floor($maxBookingTime / 60);

            // Если слот начинается в тот же день, что и начало периода, добавляем его в результат
            if ($slotStart->isSameDay($startDate)) {
                $result[] = [
                    'start' => Carbon::parse($slot['start'])->format('H:i'),
                    'min_duration' => $minDurationHours,
                    'max_duration' => $maxDurationHours,
                ];
            }
        }

        return $result;
    }

    /**
     * Получить слоты для объекта в интервале с заданным шагом
     * @param int $serviceObjectID
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $minutesStep
     * @return array
     */
    public static function getSlotsForObjectOnDate(int $serviceObjectID, Carbon $startDate, Carbon $endDate, int $minutesStep = 30): array
    {
        $orderableServiceObjectRepository = self::getOrderableServiceObjectRepository();
        $serviceObject = $orderableServiceObjectRepository->getOrderableServiceObjectById($serviceObjectID);

        if (!$serviceObject) {
            return [];
        }

        // Переводим даты в часовой пояс объекта
        $startDate->setTimezone($serviceObject->getObjectTimezone());
        $endDate->setTimezone($serviceObject->getObjectTimezone());

        $startTechnicalDateTime = $serviceObject->startTechnicalReserveDateTime;
        $endTechnicalDateTime = $serviceObject->endTechnicalReserveDateTime;

        // Предварительная загрузка всех расписаний для заданного периода
        $schedules = $orderableServiceObjectRepository->loadSchedulesForDate($serviceObject, $startDate, $endDate);

        // Предварительная загрузка всех заказов для заданного периода
        $orders = $orderableServiceObjectRepository->loadActualOrdersForDateInterval($serviceObject, $startDate, $endDate);

        // Сортируем заказы по времени начала
        $orders = $orders->sortBy(function($order) {
            return $order->start_time;
        });

        // Рассчитываем слоты и возвращаем результат
        return self::calculateTimeSlots($schedules, $orders, $startDate, $endDate, $minutesStep, $startTechnicalDateTime, $endTechnicalDateTime);
    }

    /**
     * Получить объединенные свободные слоты для объекта в интервале с заданным шагом
     * @param int $serviceObjectID
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $minutesStep
     * @return array
     */
    public static function getMergedFreeSlotsForObjectOnDate(int $serviceObjectID, Carbon $startDate, Carbon $endDate, int $minutesStep = 30): array
    {
        $orderableServiceObjectRepository = self::getOrderableServiceObjectRepository();
        $serviceObject = $orderableServiceObjectRepository->getOrderableServiceObjectById($serviceObjectID);
        if (!$serviceObject) {
            return [];
        }

        // Получаем все слоты для данного объекта и даты
        // Принудительно отключаем учет минимальной длительности, чтобы объединить все свободные слоты, а затем отрезать минимальные по длительности
        $slots = self::getSlotsForObjectOnDate($serviceObjectID, $startDate, $endDate, $minutesStep);

        // Объединяем свободные слоты по времени
        $mergedSlots = self::mergeFreeSlots($slots);

        // Получаем расписания для заданного периода
        $schedules = $orderableServiceObjectRepository->loadSchedulesForDate($serviceObject, $startDate, $endDate);

        $filteredSlots = self::filterSlotsByMinDuration($mergedSlots, $schedules);
        // Фильтруем слоты на основе минимальной длительности из правил и возвращаем результат
        return self::filterSlotsByMinDuration($filteredSlots, $schedules);
    }

    /**
     * Получить правила для слота объекта на дату и время
     * @param int $serviceObjectID
     * @param Carbon $date
     * @return array
     * @throws MoreThanOneScheduleOnDate
     */
    public static function getSlotRulesForServiceObjectOnDateAndTime(int $serviceObjectID, Carbon $date): array
    {
        $orderableServiceObjectRepository = self::getOrderableServiceObjectRepository();
        $serviceObject = $orderableServiceObjectRepository->getOrderableServiceObjectById($serviceObjectID);
        if (!$serviceObject) {
            return [];
        }

        $date->setTimezone($serviceObject->getObjectTimezone());
        $schedules = $orderableServiceObjectRepository->loadSchedulesForDate($serviceObject, $date, $date);
        if ($schedules->isEmpty()) {
            return [];
        }

        if ($schedules->count() > 1) {
            throw new MoreThanOneScheduleOnDate();
        }

        $rules = $schedules->first()->metadata['rules'] ?? [];

        return self::getSlotRule($rules, $date) ?? [];
    }


    /**
     * Проверить, можно ли разместить заказ на заданное время
     * @param int|null $orderID
     * @param int $serviceObjectID
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return void
     * @throws InvalidOrderParams
     */
    public static function checkIfOrderCanBePlacedOnTimeOrFail(?int $orderID, int $serviceObjectID, Carbon $startDate, Carbon $endDate): void
    {
        $orderableServiceObjectRepository = self::getOrderableServiceObjectRepository();
        $serviceObject = $orderableServiceObjectRepository->getOrderableServiceObjectById($serviceObjectID);
        if (!$serviceObject) {
            throw new InvalidOrderParams('Объект не найден');
        }

        // Проверяем, является ли это РЕАЛЬНЫМ редактированием существующего заказа
        // (заказ должен существовать в БД И изменяться его время)
        $isEditingExistingOrder = false;
        if ($orderID) {
            $existingOrder = Order::find($orderID);
            if ($existingOrder !== null) {
                // Проверяем, изменилось ли время заказа
                $timeChanged = !$existingOrder->start_time->equalTo($startDate) ||
                    !$existingOrder->end_time->equalTo($endDate);
                $isEditingExistingOrder = $timeChanged;
            }
        }

        // При редактировании заказа расширяем диапазон на величину service_duration,
        // чтобы захватить резервы следующих заказов для проверки
        if ($isEditingExistingOrder) {
            // Получаем правила расписания для определения service_duration
            $slotRules = self::getSlotRulesForServiceObjectOnDateAndTime($serviceObjectID, $endDate);
            $serviceDuration = isset($slotRules['service_duration']) ? (int)$slotRules['service_duration'] : 60;
            $extendedEndDate = $endDate->copy()->addMinutes($serviceDuration + 30); // +30 минут для запаса
        } else {
            $extendedEndDate = $endDate;
        }

        $slots = self::getSlotsForObjectOnDate($serviceObjectID, $startDate, $extendedEndDate, 30);

        if (count($slots) == 0) {
            throw new InvalidOrderParams('Не удалось получить слоты для объекта на заданный период');
        }

        if (!$slots[0]['can_start_booking'] && ($slots[0]['order_id'] != $orderID || !$orderID)) {
            throw new InvalidOrderParams($slots[0]['can_start_booking_reason'] ?? 'Невозможно начать бронирование в этот слот');
        }

        $startDate->setTimezone($serviceObject->getObjectTimezone());
        $endDate->setTimezone($serviceObject->getObjectTimezone());

        // Вычисляем общую длительность заказа в минутах
        $orderDuration = $startDate->diffInMinutes($endDate);

        // Переменная для накопления длительности свободных слотов
        $freeSlotDuration = 0;

        foreach ($slots as $slotIndex => $slot) {
            $slotStart = Carbon::parse($slot['start'])->shiftTimezone($serviceObject->getObjectTimezone());
            $slotEnd = Carbon::parse($slot['end'])->shiftTimezone($serviceObject->getObjectTimezone());

            // Пропускаем слоты, которые полностью после нашего времени окончания
            if ($slotStart->greaterThan($endDate)) {
                break; // Все последующие слоты тоже будут после endDate
            }

            // Проверяем, что слот занят заказом, если это не текущий заказ
            // Если слот ЗАНЯТ
            if (!$slot['is_free']) {
                // Если НЕ редактируем существующий заказ, используем старую логику с проверкой affecting_order_ids
                if (!$isEditingExistingOrder) {
                    // Старая логика: выбросить исключение, если:
                    // - нет orderID (создание нового заказа), ИЛИ
                    // - слот занят другим заказом (не нашим), ИЛИ
                    // - в слоте пересекаются 2+ заказов
                    if (!$orderID ||
                        (count($slot['affecting_order_ids']) == 1 && $slot['affecting_order_ids'][0] != $orderID) ||
                        (count($slot['affecting_order_ids']) > 1)
                    ) {
                        throw new InvalidOrderParams($slot['is_free_reason'] ?? 'Недостаточно свободного времени для размещения заказа (пересечение с другим заказом или резервом)');
                    }
                    // Если слот занят нашим заказом, пропускаем проверку (можем размещать заказ на свое же время)
                } else {
                    // ==== ЛОГИКА ДЛЯ РЕДАКТИРОВАНИЯ СУЩЕСТВУЮЩЕГО ЗАКАЗА (время изменилось) ====

                    // Проверяем резервы ПЕРЕД следующим заказом
                    if ($slot['is_free_reason'] === 'резерв на сервисное обслуживание перед следующим заказом') {
                        // Резерв ПЕРЕД другим заказом - мы НЕ можем в него влезть
                        if ($endDate->lessThanOrEqualTo($slotStart)) {
                            // Заказ заканчивается до начала резерва - это разрешено
                        } else {
                            // Заказ пересекает резерв - это запрещено
                            throw new InvalidOrderParams($slot['is_free_reason'] ?? 'Недостаточно свободного времени для размещения заказа (пересечение с резервом)');
                        }
                    } elseif ($slot['is_free_reason'] === 'резерв на сервисное обслуживание после заказа') {
                        // Резерв ПОСЛЕ заказа - проверяем, чей это заказ
                        $isOurReserve = !empty($slot['affecting_order_ids']) && in_array($orderID, $slot['affecting_order_ids']);

                        if (!$isOurReserve) {
                            // Это резерв после ДРУГОГО заказа - мы не можем в него влезть
                            if ($endDate->lessThanOrEqualTo($slotStart)) {
                                // Заказ заканчивается до начала резерва - это разрешено
                            } else {
                                // Заказ пересекает резерв - это запрещено
                                throw new InvalidOrderParams($slot['is_free_reason'] ?? 'Недостаточно свободного времени для размещения заказа (пересечение с резервом)');
                            }
                        } else {
                            // Это резерв ПОСЛЕ нашего заказа
                            // Проверяем, пересекается ли наше продление с резервом перед следующим заказом
                            // Резерв нашего заказа можно использовать, ЕСЛИ мы не влезаем в резерв ПЕРЕД следующим заказом

                            // Ищем следующий заказ в последующих слотах
                            $nextOrderStartTime = null;

                            for ($i = $slotIndex + 1; $i < count($slots); $i++) {
                                $nextSlot = $slots[$i];
                                $nextSlotStart = Carbon::parse($nextSlot['start'])->shiftTimezone($serviceObject->getObjectTimezone());

                                // Ищем резерв ПЕРЕД следующим заказом (не нашим)
                                if (!$nextSlot['is_free'] &&
                                    $nextSlot['is_free_reason'] === 'резерв на сервисное обслуживание перед следующим заказом') {
                                    // Резерв найден - значит следующий заказ начнется после этого резерва
                                    // Нужно получить service_duration из расписания
                                    $slotRules = self::getSlotRulesForServiceObjectOnDateAndTime($serviceObjectID, $nextSlotStart);
                                    $serviceDuration = isset($slotRules['service_duration']) ? (int)$slotRules['service_duration'] : 60;
                                    $nextOrderStartTime = $nextSlotStart->copy()->addMinutes($serviceDuration);
                                    break;
                                }

                                // Или находим сам следующий заказ (не наш)
                                if (!$nextSlot['is_free'] &&
                                    strpos($nextSlot['is_free_reason'], 'существует заказ #') !== false &&
                                    $nextSlot['order_id'] != $orderID) {
                                    // Следующий заказ найден
                                    $nextOrderStartTime = $nextSlotStart;
                                    break;
                                }
                            }

                            if ($nextOrderStartTime !== null) {
                                // Есть следующий заказ
                                // Вычисляем начало резерва перед ним (service_duration минут до начала заказа)
                                $slotRules = self::getSlotRulesForServiceObjectOnDateAndTime($serviceObjectID, $nextOrderStartTime);
                                $serviceDuration = isset($slotRules['service_duration']) ? (int)$slotRules['service_duration'] : 60;
                                $nextOrderReserveStart = $nextOrderStartTime->copy()->subMinutes($serviceDuration);

                                // Проверяем, влезаем ли мы в резерв перед следующим заказом
                                if ($endDate->greaterThan($nextOrderReserveStart)) {
                                    // Наше окончание влезает в резерв перед следующим заказом - запрещено
                                    throw new InvalidOrderParams('Недостаточно свободного времени для размещения заказа (пересечение с резервом перед следующим заказом)');
                                }
                            }
                            // Если нет следующего заказа или мы не влезаем в его резерв, можем использовать свой резерв (резерв просто сдвинется)
                        }
                    }

                    // Проверяем стандартные условия для редактирования:
                    // - Если слот занят одним заказом, и это не текущий редактируемый заказ
                    // - Или если в слот пересекаются 2 и более заказов
                    if ((count($slot['affecting_order_ids']) == 1 && $slot['affecting_order_ids'][0] != $orderID) ||
                        (count($slot['affecting_order_ids']) > 1)
                    ) {
                        throw new InvalidOrderParams($slot['is_free_reason'] ?? 'Недостаточно свободного времени для размещения заказа (пересечение с другим заказом или резервом)');
                    }
                }
            }

            // Если слот свободен и подходит для заказа, увеличиваем длительность свободного интервала на шаг (30 минут)
            if ($slotStart->greaterThanOrEqualTo($startDate) && $slotEnd->lessThanOrEqualTo($endDate)) {
                $freeSlotDuration += $slotStart->diffInMinutes($slotEnd);
            }
            if ($freeSlotDuration >= $orderDuration) {
                break;
            }
        }


        // Если длительность свободных интервалов больше или равна длительности заказа, заказ можно разместить
        if ($freeSlotDuration < $orderDuration) {
            throw new InvalidOrderParams('Недостаточно свободного времени для размещения заказа (пересечение с другим заказом или резервом)');
        }
    }

    /**
     * Проверить, можно ли разместить технический резерв на заданное время
     * @param OrderableServiceObject $serviceObject
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return void
     * @throws InvalidOrderParams
     */
    public static function checkIfTechnicalReserveCanBePlacedOnTimeOrFail(OrderableServiceObject $serviceObject, Carbon $startDate, Carbon $endDate): void
    {
        $slots = self::getSlotsForObjectOnDate($serviceObject->id, $startDate, $endDate, 30);

        if (count($slots) == 0) {
            throw new InvalidOrderParams('Не удалось получить слоты для объекта на заданный период');
        }

        // Вычисляем общую длительность заказа в минутах
        $orderDuration = $startDate->diffInMinutes($endDate);

        // Переменная для накопления длительности свободных слотов
        $freeSlotDuration = 0;

        foreach ($slots as $slot) {
            if (!$slot['is_free']) {
                throw new InvalidOrderParams($slot['is_free_reason'] ?? 'Недостаточно свободного времени для размещения заказа (пересечение с другим заказом или резервом)');
            }
            $slotStart = Carbon::parse($slot['start'])->shiftTimezone($serviceObject->getObjectTimezone());
            $slotEnd = Carbon::parse($slot['end'])->shiftTimezone($serviceObject->getObjectTimezone());
            // Если слот свободен и подходит для заказа, увеличиваем длительность свободного интервала на шаг (30 минут)
            if ($slotStart->greaterThanOrEqualTo($startDate) && $slotEnd->lessThanOrEqualTo($endDate)) {
                $freeSlotDuration += $slotStart->diffInMinutes($slotEnd);
            }
            if ($freeSlotDuration >= $orderDuration) {
                break;
            }
        }

        // Если длительность свободных интервалов больше или равна длительности заказа, заказ можно разместить
        if ($freeSlotDuration < $orderDuration) {
            throw new InvalidOrderParams('Недостаточно свободного времени для размещения заказа (пересечение с другим заказом или резервом)');
        }
    }

    // TODO: строгая типизация для правил
    // {
    //      "rules": {
    //          "week_days": [
    //              {"time_to": "23:59", "weekdays": ["1", "2", "3", "4"], "time_from": "06:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"time_to": "05:59", "weekdays": ["2", "3", "4"], "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"time_to": "05:59", "weekdays": ["5"], "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"time_to": "23:59", "weekdays": ["5", "6", "7"], "time_from": "06:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"time_to": "05:59", "weekdays": ["6", "7"], "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"time_to": "05:59", "weekdays": ["1"], "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"}
    //          ],
    //          "concrete_days": [
    //              {"days": ["07-05-2024", "08-05-2024", "09-05-2024"], "time_to": "23:59", "time_from": "06:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"days": ["08-05-2024", "09-05-2024", "10-05-2024"], "time_to": "05:59", "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"days": ["17-06-2024"], "time_to": "23:59", "time_from": "06:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"days": ["18-06-2024"], "time_to": "05:59", "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"days": ["08-07-2024"], "time_to": "23:59", "time_from": "06:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"},
    //              {"days": ["09-07-2024"], "time_to": "05:59", "time_from": "00:00", "max_duration": "1440", "min_duration": "120", "service_duration": "60", "confirmation_waiting_duration": "60"}
    //           ]
    //     }
    // }

    private static function getDurationsFromSchedule($schedules, Carbon $date, array $slot): array
    {
        $defaultMinDuration = null;
        $defaultMaxDuration = null;

        $schedule = self::getScheduleForDate($schedules, $date);

        if (!$schedule) {
            return [$defaultMinDuration, $defaultMaxDuration];
        }

        $rules = $schedule->metadata['rules'];
        $time = Carbon::parse($slot['start'])->toTimeString();

        $date = Carbon::parse($date->format('Y-m-d') . ' ' . $time);

        $slotRule = self::getSlotRule($rules, $date);

        if ($slotRule) {
            return [(int)$slotRule['min_duration'], (int)$slotRule['max_duration']];
        }

        return [$defaultMinDuration, $defaultMaxDuration];
    }

    /**
     * Рассчитать слоты для объекта на диапазон дат с заданным шагом
     * @param $schedules
     * @param $orders
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $minutesStep
     * @param Carbon|null $startTechnicalDateTime
     * @param Carbon|null $endTechnicalDateTime
     * @return array
     */
    private static function calculateTimeSlots($schedules, $orders, Carbon $startDate, Carbon $endDate, int $minutesStep = 30, ?Carbon $startTechnicalDateTime = null, ?Carbon $endTechnicalDateTime = null): array
    {
        // Преобразовываем дату с точностью до получаса (округляем к ближайшему получасу вниз)
        $start = self::roundToMinutes($startDate->copy(), $minutesStep);
        $end = self::roundToMinutes($endDate->copy(), $minutesStep);
        $slots = [];

        // Если $start больше $end, возвращаем пустой массив
        if ($start->greaterThan($end)) {
            return $slots;
        }

        // Проходим по всем слотам с шагом в 30 минут
        for ($current = $start->copy(); $current->lessThanOrEqualTo($end); $current->addMinutes($minutesStep)) {
            $slots[] = self::createSlot($schedules, $orders, $current, $minutesStep, $startTechnicalDateTime, $endTechnicalDateTime);
        }

        return self::markIfSlotCanStartBookingByMinDuration($slots, $schedules);
    }

    /**
     * Создать слот для заданной даты с учетом расписаний, заказов и шага
     * @param $schedules
     * @param $orders
     * @param Carbon $current
     * @param int $minutesStep
     * @param Carbon|null $startTechnicalDateTime
     * @param Carbon|null $endTechnicalDateTime
     * @return array
     */
    private static function createSlot($schedules, $orders, Carbon $current, int $minutesStep, ?Carbon $startTechnicalDateTime, ?Carbon $endTechnicalDateTime): array
    {
        $current = $current->copy()->setSecond(0)->setMicrosecond(0);
        // Формируем слот изначально как свободный
        $slot = [
            'start' => $current->toDateTimeString(),
            'end' => $current->copy()->addMinutes($minutesStep)->toDateTimeString(),
            'is_free' => true, // свободен ли слот
            'is_free_reason' => null,
            'can_start_booking' => false, // можно ли начать бронирование в этот слот
            'can_start_booking_reason' => null,
            'order_id' => null,
            'affecting_order_ids' => [],
        ];
        //Проверяем есть правила на заданный промежуток, если нет, то слот занят

        $scheduleForDate = self::getScheduleForDate($schedules, $current);
        if (!$scheduleForDate) {
            $slot['is_free'] = false;
            $slot['can_start_booking'] = false;
            $slot['is_free_reason'] = 'нет расписания на этот день';
            $slot['can_start_booking_reason'] = $slot['is_free_reason'];
            return $slot;
        }

        // Получаем правило для слота
        $rules = $scheduleForDate->metadata['rules'];
        $slotRule = self::getSlotRule($rules, $current);

        // Если правила нет, то слот занят
        if (!$slotRule) {
            $slot['is_free'] = false;
            $slot['can_start_booking'] = false;
            $slot['is_free_reason'] = 'нерабочее время';
            $slot['can_start_booking_reason'] = $slot['is_free_reason'];
            return $slot;
        }

        if ($startTechnicalDateTime && $endTechnicalDateTime) {
            // Check if current slot overlaps with technical maintenance period
            $slotStart = $current;
            $slotEnd = $current->copy()->addMinutes($minutesStep);

            if ($slotEnd->greaterThan($startTechnicalDateTime) && $slotStart->lessThan($endTechnicalDateTime)) {
                $slot['is_free'] = false;
                $slot['is_free_reason'] = 'технический резерв';
                $slot['can_start_booking'] = false;
                $slot['can_start_booking_reason'] = $slot['is_free_reason'];
                return $slot;
            }
        }

        // Проверяем занятость слота по заказам
        // TODO: optimize
        $occupiedOrderFoundIndex = null;
        foreach ($orders as $index => $order) {
            list($isFree, $isFreeReason, $canStartBooking, $canStartBookingReason, $orderId) = self::getSlotOccupationDataByOrder($order, $current, $schedules);
            $isSlotFree = $isFree;
            $slot['is_free'] = $isSlotFree;
            $slot['is_free_reason'] = $isFreeReason;
            $slot['can_start_booking'] = $canStartBooking;
            $slot['can_start_booking_reason'] = $canStartBookingReason;
            $slot['order_id'] = $orderId;
            if ($orderId) {
                $slot['affecting_order_ids'][] = $orderId;
                $occupiedOrderFoundIndex = $index;
            }
            if (!$isSlotFree) {
                break;
            }
        }

        if ($occupiedOrderFoundIndex !== null) {
            $nextOrderIndex = $occupiedOrderFoundIndex + 1;
            if (isset($orders[$nextOrderIndex])) {
                $nextOrder = $orders[$nextOrderIndex];
                list($isFree, $isFreeReason, $canStartBooking, $canStartBookingReason, $orderId) = self::getSlotOccupationDataByOrder($nextOrder, $current, $schedules);
                if (!$isFree && $orderId) {
                    $slot['affecting_order_ids'][] = $orderId;
                }
            }
        }

        // Если слот в прошлом - считаем его занятым
        if ($current->lessThan(Carbon::now())) {
            $slot['is_free'] = false;
            $slot['is_free_reason'] = 'слот в прошлом';
            $slot['can_start_booking_reason'] = $slot['is_free_reason'];
            return $slot;
        }

        return $slot;
    }


    /**
     * Получить данные о занятости слота по заказу
     * Учитывает резерв на сервисное обслуживание до и после заказа, а так же диапазон самого заказа
     * @param Order $order
     * @param Carbon $current
     * @param Collection $schedules
     * @return array [is_free, is_free_reason, can_start_booking, can_start_booking_reason, order_id]
     */
    private static function getSlotOccupationDataByOrder(Order $order, Carbon $current, Collection $schedules): array
    {
        // Получаем время начала и окончания заказа и преобразуем их в часовой пояс объекта
        $orderStartTime = Carbon::parse($order->start_time)->setTimezone($order->orderableServiceObject->getObjectTimezone());
        $orderEndTime = Carbon::parse($order->end_time)->setTimezone($order->orderableServiceObject->getObjectTimezone());

        $orderStartSchedule = self::getScheduleForDate($schedules, $orderStartTime);
        $orderEndSchedule = self::getScheduleForDate($schedules, $orderEndTime);

        $orderSlotRuleOnOrderStart = $orderStartSchedule ? self::getSlotRule($orderStartSchedule->metadata['rules'] ?? [], $orderStartTime) : null;
        $orderSlotRuleOnOrderEnd = $orderEndSchedule ? self::getSlotRule($orderEndSchedule->metadata['rules'] ?? [], $orderEndTime) : null;

        // Проверяем занятость с учетом резерва на сервисное обслуживание перед следующим заказом
        if ($orderSlotRuleOnOrderStart) {
            $orderServiceDurationBeforeOrder = (int)($orderSlotRuleOnOrderStart['service_duration'] ?? 60);
            if ($current->between($orderStartTime->copy()->subMinutes($orderServiceDurationBeforeOrder), $orderStartTime->copy()->subMinute(), true)) {
                $isFreeReason = 'резерв на сервисное обслуживание перед следующим заказом';
                $canStartBookingReason = $isFreeReason;
                return [false, $isFreeReason, false, $canStartBookingReason, $order->id];
            }
        }

        // Проверяем занятость с учетом заказа
        if ($current->between($orderStartTime, $orderEndTime->copy()->subMinute(), true)) {
            $isFreeReason = 'существует заказ # ' . $order->id . ' в этот интервал';
            $canStartBookingReason = $isFreeReason;
            return [false, $isFreeReason, false, $canStartBookingReason, $order->id];
        }

        // Проверяем занятость с учетом сервисного обслуживания после заказа
        if ($orderSlotRuleOnOrderEnd) {
            $orderServiceDurationAfterOrder = (int)($orderSlotRuleOnOrderEnd['service_duration'] ?? 60);
            if ($orderServiceDurationAfterOrder == 0) {
                return [true, null, true, null, null];
            }
            if ($current->between($orderEndTime, $orderEndTime->copy()->addMinutes($orderServiceDurationAfterOrder)->subMinute(), true)) {
                $isFreeReason = 'резерв на сервисное обслуживание после заказа';
                $canStartBookingReason = $isFreeReason;
                return [false, $isFreeReason, false, $canStartBookingReason, $order->id];
            }
        }

        // TODO: строгая типизация на все это (и то, что выше)
        return [true, null, true, null, null];
    }

    /**
     * Пометить слоты, которые не соответствуют минимальной длительности невозможными для начала бронирования
     * @param array $slots
     * @param $schedules
     * @return array
     */
    private static function markIfSlotCanStartBookingByMinDuration(array $slots, $schedules): array
    {
        // Проходим по всем слотам
        for ($i = 0; $i < count($slots); $i++) {
            // Если слот свободен
            if ($slots[$i]['is_free']) {
                // Получаем начало интервала слота
                $intervalStart = Carbon::parse($slots[$i]['start']);

                // Получаем расписание на начало интервала
                $scheduleForDate = self::getScheduleForDate($schedules, $intervalStart);

                // Если расписания нет, переходим к следующему слоту
                if (!$scheduleForDate) {
                    continue;
                }

                // Получаем правило для слота
                $rules = $scheduleForDate->metadata['rules'];
                $slotRule = self::getSlotRule($rules, $intervalStart);

                // Если правила нет, переходим к следующему слоту
                if (!$slotRule) {
                    continue;
                }

                // Получаем минимальную длительность слота
                $minDuration = (int)($slotRule['min_duration'] ?? 120);

                // Помечаем слот занятым, если он не соответствует минимальной длительности
                list($isSlotCanStartBookingByMinimalDuration, $occupyingOrderId) = self::isSlotCanStartBookingByMinimalDuration($slots, $i, $intervalStart, $minDuration);
                $slots[$i]['can_start_booking'] = $isSlotCanStartBookingByMinimalDuration;
                if (!$isSlotCanStartBookingByMinimalDuration) {
                    $slots[$i]['can_start_booking_reason'] = 'интервал меньше минимальной длительности для начала бронирования';
                    if ($occupyingOrderId) {
                        $slots[$i]['order_id'] = $occupyingOrderId;
                        $slots[$i]['can_start_booking_reason'] .= ' из-за заказа # ' . $occupyingOrderId;
                    }
                }
            }
        }
        return $slots;
    }

    /**
     * Обработать интервалы слотов по минимальной длительности
     * @param array $slots
     * @param int $startIndex
     * @param Carbon $intervalStart
     * @param int $minDuration
     * @return array
     */
    private static function isSlotCanStartBookingByMinimalDuration(array $slots, int $startIndex, Carbon $intervalStart, int $minDuration): array
    {
        $duration = 0;
        $occupyingOrderId = null;

        // Проходим по всем слотам, начиная с текущего
        for ($j = $startIndex; $j < count($slots); $j++) {

            // Если слот занят, прерываем цикл и запоминаем заказ
            if (!$slots[$j]['is_free']) {
                $occupyingOrderId = $slots[$j]['order_id'];
                break;
            }

            // Получаем конец интервала слота
            $intervalEnd = Carbon::parse($slots[$j]['end']);
            $duration = $intervalStart->diffInMinutes($intervalEnd);

            if ($duration >= $minDuration) {
                break;
            }
        }

        return [$duration >= $minDuration, $occupyingOrderId];
    }

    /**
     * Округлить дату до ближайшего шага
     * @param Carbon $dateTime
     * @param int $step
     * @param string $direction (up|down)
     * @return Carbon
     */
    private static function roundToMinutes(Carbon $dateTime, int $step = 30, string $direction = 'down'): Carbon
    {
        $minutes = $dateTime->minute;
        $roundedMinutes = $direction === 'up'
            ? ceil($minutes / $step) * $step
            : floor($minutes / $step) * $step;

        return $dateTime->copy()->minute($roundedMinutes)->second(0);
    }

    /**
     * Получить расписание для заданной даты
     * @param $schedules
     * @param Carbon $date
     * @return Schedule|null
     */
    private static function getScheduleForDate($schedules, Carbon $date): ?Schedule
    {
        return $schedules->first(function ($schedule) use ($date) {
            return $date->greaterThanOrEqualTo($schedule->pivot->date_from);
        });
    }

    /**
     * Получить правило по заданной дате
     * @param array $rules
     * @param Carbon $date
     * @return array|null
     */
    private static function getSlotRule(array $rules, Carbon $date): ?array
    {
        if (!isset($rules['week_days']) && !isset($rules['concrete_days'])) {
            return [];
        }

        // Получаем день недели, время и дату в формате "день-месяц-год"
        $dayOfWeek = $date->dayOfWeekIso;
        $day = $date->format('d-m-Y');
        $time = $date->toTimeString();

        $slotRule = null;

        if (isset($rules['concrete_days']) && count($rules['concrete_days']) > 0) {
            $slotRule = collect($rules['concrete_days'])->first(function ($rule) use ($day, $time) {
                return in_array($day, $rule['days']) && $time >= $rule['time_from'] && $time <= $rule['time_to'];
            });
        }

        if (!$slotRule && isset($rules['week_days']) && count($rules['week_days']) > 0) {
            $slotRule = collect($rules['week_days'])->first(function ($rule) use ($dayOfWeek, $time) {
                return in_array($dayOfWeek, $rule['weekdays']) && $time >= $rule['time_from'] && $time <= $rule['time_to'];
            });
        }

        return $slotRule;
    }


    /**
     * Объединить свободные слоты по времени
     * @param array $slots
     * @return array
     */
    private static function mergeFreeSlots(array $slots): array
    {
        $mergedSlots = [];
        $currentRangeStart = null;

        foreach ($slots as $slot) {
            if ($slot['is_free']) {
                // Начинаем новый свободный диапазон, если это первый свободный слот
                if (!$currentRangeStart) {
                    $currentRangeStart = $slot['start'];
                }
            } else {
                // Завершаем текущий свободный диапазон, если текущий слот занят
                if ($currentRangeStart) {
                    $mergedSlots[] = [
                        'start' => $currentRangeStart,
                        'end' => $slot['start']  // Предыдущий конец диапазона – начало текущего занятого слота
                    ];
                    $currentRangeStart = null;
                }
            }
        }

        // Добавляем последний свободный диапазон, если он еще не закрыт
        if ($currentRangeStart) {
            $mergedSlots[] = [
                'start' => $currentRangeStart,
                'end' => $slots[count($slots) - 1]['end']  // Конец последнего слота в списке
            ];
        }

        return $mergedSlots;
    }

    /**
     * Отфильтровать слоты по минимальной длительности в рамках правил из расписаний
     * @param array $slots
     * @param $schedules
     * @return array
     */
    private static function filterSlotsByMinDuration(array $slots, $schedules): array
    {
        $filteredSlots = [];

        foreach ($slots as $slot) {
            $start = Carbon::parse($slot['start']);
            $end = Carbon::parse($slot['end']);
            $duration = $start->diffInMinutes($end);

            // Получаем расписание на начало слота
            $scheduleForDate = self::getScheduleForDate($schedules, $start);

            if (!$scheduleForDate) {
                continue;
            }

            $rules = $scheduleForDate->metadata['rules'];
            $slotRule = self::getSlotRule($rules, $start);

            if ($slotRule && $duration >= (int)($slotRule['min_duration'] ?? 120)) {
                $filteredSlots[] = $slot;
            }
        }

        return $filteredSlots;
    }

    public static function filterServiceDurationSlots(array $allSlots, Collection $schedules, Carbon $startDate, OrderableServiceObject $object): array
    {
        // Получаем расписание для заданной даты
        $schedule = self::getScheduleForDate($schedules, $startDate);
        if (!$schedule) {
            return $allSlots;
        }

        // Извлекаем правила из метаданных расписания
        $rules = $schedule->metadata['rules'];
        $slotRule = self::getSlotRule($rules, $startDate);

        if (!$slotRule || !isset($slotRule['service_duration'])) {
            return $allSlots;
        }

        $serviceDuration = (int)$slotRule['service_duration'];

        $filteredSlots = [];
        $now = Carbon::now()->second(0)->microsecond(0); // Убираем секунды и микросекунды из текущего времени
        $minimalStartDate = $now->addMinutes($serviceDuration)->setTimezone($object->getObjectTimezone());

        foreach ($allSlots as $slot) {
            $slotStart = Carbon::parse($slot['start'], $object->getObjectTimezone());
            // Проверяем, начинается ли слот после $adjustedStartDate и соответствует ли его длительность
            if ($slotStart->greaterThanOrEqualTo($minimalStartDate)) {
                $filteredSlots[] = $slot;
            }
        }

        return $filteredSlots;
    }
}
