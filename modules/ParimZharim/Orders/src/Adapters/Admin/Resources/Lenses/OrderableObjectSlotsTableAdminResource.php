<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses;

use DigitalCreative\MegaFilter\MegaFilter;
use DigitalCreative\MegaFilter\MegaFilterTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\ObjectCategoryFilter;
use Modules\ParimZharim\Orders\Adapters\Admin\Filters\ObjectDateFilter;
use Modules\ParimZharim\Orders\Application\Actions\GetMergedFreeSlotsForServiceObjectOnDate;

class OrderableObjectSlotsTableAdminResource extends Lens
{
    use MegaFilterTrait;


    public static $polling = true;

    public static $pollingInterval = 30;

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     */
    public function name()
    {
        return 'Таблица Резервов';
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        $objects = $request->withOrdering($request->withFilters($query))->get();

        // Используйте дату из запроса или текущую дату, если фильтр не установлен
        $date = $request->get('order_date', now());
        $date->setTimezone('Asia/Almaty');
        $date->setTime(12, 0, 0);

        // Добавляем информацию о свободных слотах к каждому объекту
        $objects->each(function ($object) use ($date) {
            $slots = GetMergedFreeSlotsForServiceObjectOnDate::make()->handle($object->id, $date);
            $object->free_slots = $slots;
            $object->free_slots_count = count($slots) ?: 0;
        });

        // Сортируем объекты по количеству свободных слотов
        $sortedObjects = $objects->sort(function ($a, $b) {
            return ($b->free_slots_count ?? 0) <=> ($a->free_slots_count ?? 0);
        });

        // Поскольку мы уже изменили коллекцию, просто пагинируем её вручную
        $perPage = self::perPage($request); // или напрямую укажите число, например, 100
        $page = $request->get('page', 1);
        $paginator = new LengthAwarePaginator(
            $sortedObjects->values()->all(),
            $sortedObjects->count(),
            $perPage,
            $page
        );

        return $paginator;
    }

    /**
     * Get the fields available to the lens.
     *
     * @param NovaRequest $request
     * @return array
     * @throws HelperNotSupported
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Text::make('ID', 'id'),
            Text::make('Название', 'name'),
            Text::make('Свободные слоты', 'free_slots')
                ->asHtml()
                ->displayUsing(function ($value) use ($request) {
                    if (!$value) {
                        return 'Нет свободных слотов';
                    }
                    $now = Carbon::now()->setTimezone('Asia/Almaty');
                    return implode(', ', array_map(function ($slot) use ($now) {
                        $startTime = Carbon::parse($slot['start']);
                        $endTime = Carbon::parse($slot['end']);

                        // если время начала меньше, чем текущее время - отображать start как следующий ближайший час
                        if ($startTime->lessThan($now)) {
                            $startTime = $now->addHour()->setTime($now->hour, 0, 0);
                        }

                        // Форматируем начальное время как "ПН, 9 мая 10:00"
                        $formattedStart = $startTime->isoFormat('dd, D MMMM HH:mm');

                        // Форматируем конечное время как "12:00" если это тот же самый день
                        if ($startTime->isSameDay($endTime)) {
                            $formattedEnd = $endTime->isoFormat('HH:mm');
                        } else {
                            // Иначе форматируем как "ВТ, 10 мая 12:00"
                            $formattedEnd = $endTime->isoFormat('dd, D MMMM HH:mm');
                        }

                        // Возвращаем форматированный временной интервал с использованием баджиков
                        return '<span class="badge" style="background-color:green; color: white; padding: 5px 10px; border-radius: 5px;">' . $formattedStart . ' - ' . $formattedEnd . '</span>';
                    }, $value));
                }),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        //add name
        return [
            MegaFilter::make([
                new ObjectCategoryFilter(),
                new ObjectDateFilter()
            ])->columns(3),
        ];
    }

    /**
     * Получить доступные опции пагинации для линзы.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return array
     */
    public static function perPageOptions(LensRequest $request)
    {
        return [100];
    }

    /**
     * Определите, сколько ресурсов должно быть показано на странице по умолчанию.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return int
     */
    public static function perPage(LensRequest $request)
    {
        return 100;
    }
}
