<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Adapters\Admin;

use Carbon\CarbonInterval;
use Devcraft\DatetimeWoTimezone\DatetimeWoTimezone;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\Notification\Domain\Models\Channel;
use Modules\Shared\Notification\Domain\Models\Notification;
use Outl1ne\MultiselectField\Multiselect;
use Throwable;

class NotificationAdminResource extends BaseAdminResource
{

    public static string $model = Notification::class;

    public static $search = [
        'id', 'title', 'body', 'channels'
    ];

    public static $title = 'title';

    public static function label(): string
    {
        return 'Уведомления';
    }

    public static function createButtonLabel(): ?string
    {
        return 'Создать уведомление';
    }

    public static function singularLabel(): string
    {
        return 'Уведомление';
    }


    public static $perPageViaRelationship = 25;

    public function fields(NovaRequest $request): array
    {
        Carbon::setLocale('ru');
        return [
            Text::make('Заголовок', 'title')
                ->sortable()
                ->rules('required', 'max:50'),

            Textarea::make('Текст уведомления', 'body')
                ->rules('required', 'max:150')
                ->alwaysShow(),
            Text::make('Телефон', 'metadata->phone')
                ->sortable()
                ->rules('required', 'max:50')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail(function () {
                    return !isset($this->metadata['phone']);
                }),

//            Multiselect::make('Каналы', 'channels')
//                ->options(Channel::labels())
//                ->displayUsing(function ($value) {
//                    if (is_array($value)) {
//                        // If value is an array, map it to channel labels
//                        return collect($value)->map(function ($channel) {
//                            if ($channel instanceof Channel) {
//                                // If the channel is an object, access its properties
//                                return Channel::labels()[$channel->value] ?? $channel->value;
//                            }
//                            // If it's not an object, return it directly
//                            return Channel::labels()[$channel] ?? $channel;
//                        })->implode(', ');
//                    }
//
//                    // If it's a single value, check if it's an instance of Channel
//                    if ($value instanceof Channel) {
//                        return Channel::labels()[$value->value] ?? $value->value;
//                    }
//
//                    // Return the single value label
//                    return Channel::labels()[$value] ?? $value;
//                })
//                ->sortable()
//                ->rules('required'),

            DatetimeWoTimezone::make('Дата отправки', 'planed_send_at')
                ->displayUsing(function ($value) use ($request) {
                    $convertedValue = $this->convertUtcToTimezone($value, $request);
                    return $convertedValue ? $convertedValue->isoFormat('dd, D MMMM YYYY HH:mm') : 'Нет данных';
                })
                ->resolveUsing(function ($value) use ($request) {
                    return $this->convertUtcToTimezone($value, $request, true);
                })
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = $this->convertTimezoneToUtc($request->input($requestAttribute), $request);
                })
                ->rules('required', 'date')
                ->step(CarbonInterval::minute())
                ->sortable(),
        ];
    }

    public function convertUtcToTimezone($value, NovaRequest $request, bool $forEditing = false): ?Carbon
    {
        $timezone = 'Asia/Almaty';

        if (!$value) {
            return null;
        }

        try {
            if ($forEditing) {
                $offset = Carbon::now($timezone)->utcOffset();
                return $value->addMinutes($offset);
            }

            return $value->setTimezone($timezone);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function convertTimezoneToUtc($value, NovaRequest $request): ?Carbon
    {
        $timezone = 'Asia/Almaty';

        try {
            $localTime = Carbon::parse($value, 'UTC');
            $targetOffset = Carbon::now($timezone)->getOffset();
            return $localTime->subSeconds($targetOffset)->setTimezone('UTC');
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
