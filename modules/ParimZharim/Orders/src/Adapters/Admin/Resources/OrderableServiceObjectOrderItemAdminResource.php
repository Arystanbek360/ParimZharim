<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceObjectOrderItem;

class OrderableServiceObjectOrderItemAdminResource extends Resource
{
    public static $model = OrderableServiceObjectOrderItem::class;
    public static $title = 'id';
    public static $search = ['id'];

    public static function label(): string
    {
        return 'Заказываемый объект';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Заказ', 'order', OrderAdminResource::class)
                ->sortable()
                ->rules('required')
                ->searchable(),

            BelongsTo::make('Объект', 'orderable', OrderableServiceObjectAdminResource::class)
                ->sortable()
                ->rules('required'),

            Currency::make('Стоимость', 'total_with_discount')
            ->currency('KZT')
            ->sortable(),

            // Implement the complex logic to display and calculate metadata
            // similar to the implementation in OrderAdminResource
            Code::make('Расчет стоимости бронирования', 'metadata')
                ->json()
                ->hideFromIndex()
                ->onlyOnDetail()
                ->displayUsing(function ($value) {
                    $data = json_decode($value, true);
                    $lines = [];

                    $additionalGuestsCount = 0;
                    $guestLimit = 0;
                    $guestLimitExtraGuestFee = 0;

                    if (isset($data['totalObjectBookingPrice'])) {
                        $lines[] = "-----------------------------------\n";
                        $lines[] = "Общая цена за бронирование: {$data['totalObjectBookingPrice']} KZT\n";
                    }

                    if (isset($data['discount']) && isset($data['discountPrice'])) {
                        $lines[] = "Скидка: {$data['discount']} % -  {$data['discountPrice']} KZT\n";
                    }

                    if (isset($data['totalObjectBookingPrice']) && isset($data['discountPrice']))
                    {
                        $priceWithDiscount = $data['totalObjectBookingPrice'] - $data['discountPrice'];
                        $lines[] = "Цена со скидкой: $priceWithDiscount KZT\n";
                    }

                    // Для правил плана
                    if (isset($data['planRules'])) {
                        $lines[] = "-----------------------------------\n";
                        $lines[] = "Правила тарфиа по гостям:";
                        if (isset($data['planRules']['guest_limit'])) {
                            $guestLimit = $data['planRules']['guest_limit']['count'] ?? 0;
                            $guestLimitExtraGuestFee = $data['planRules']['guest_limit']['extra_guest_fee'] ?? 0;
                            $lines[] = "Лимит гостей: {$guestLimit}, цена за дополнительного гостя: {$guestLimitExtraGuestFee}";
                        }
                    }

                    $lines[] = "";



                    // Для дополнительных гостей
                    if (isset($data['priceDetails']['by_guests'])) {
                        $details = $data['priceDetails']['by_guests'];
                        $guestCount = $details['guests'] ?? 0;
                        $additionalGuestsCount = $guestCount - $guestLimit;
                        if ($additionalGuestsCount < 0) {
                            $additionalGuestsCount = 0;
                        }
                        $additionalGuestsPrice = $additionalGuestsCount * $guestLimitExtraGuestFee;
                        $lines[] = "Итого гостей: {$guestCount}, из них дополнительные {$additionalGuestsCount}\nЦена за доп гостей: {$additionalGuestsPrice}\n";
                        $lines[] = "-----------------------------------\n";
                    }

                    // Для деталей по времени
                    if (isset($data['priceDetails']) && isset($data['priceDetails']['by_time'])) {
                        $lines[] = "Детали тарификации по времени:\n";
                        foreach ($data['priceDetails']['by_time'] as $time) {
                            $lines[] = "Дата и время: {$time['date']}, Цена: {$time['price']}, Описание: {$time['description']}";
                        }
                    }


                    return implode("\n", $lines);
                }),
        ];
    }
}
