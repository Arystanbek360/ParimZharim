<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Carbon\CarbonInterval;
use Devcraft\DatetimeWoTimezone\DatetimeWoTimezone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Application\Actions\SetTechnicalReserveForOrderableServiceObject;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;

class SetTechnicalReserveForOrderableServiceObjectAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            $timezone = $model->getObjectTimezone();
            // Convert the start date to UTC
            $start = $this->convertTimezoneToUtc((string)$fields->start_technical_reserve, $timezone);
            $end = $this->convertTimezoneToUtc((string)$fields->end_technical_reserve, $timezone);
            SetTechnicalReserveForOrderableServiceObject::make()->handle((int)$model->id, $start, $end);
        }

    }


    public function fields(NovaRequest $request): array
    {
        return [
            DatetimeWoTimezone::make('Дата начала технического резерва', 'start_technical_reserve')
                ->step(CarbonInterval::minutes(30))
                ->rules('required', 'date'),
            DatetimeWoTimezone::make('Дата окончания технического резерва', 'end_technical_reserve')
                ->step(CarbonInterval::minutes(30))
                ->rules('required', 'date')
        ];

    }

    public function name(): string
    {
        return 'Установить технический резерв';
    }

    private function convertTimezoneToUtc(string $inputDate, string $timezone): ?Carbon
    {
        try {
            // Сначала парсим дату как UTC
            $localTime = Carbon::parse($inputDate, 'UTC');

            // Получаем смещение между нужным часовым поясом и UTC (в секундах)
            $targetOffset = Carbon::now($timezone)->getOffset();

            // Вычитаем это смещение, чтобы откорректировать время в UTC
            return $localTime->subSeconds($targetOffset)->setTimezone('UTC');
        } catch (\Throwable $e) {
            // Логируем ошибку для дальнейшего анализа
            throw $e;
        }
    }

}
