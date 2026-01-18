<?php

namespace Rpj\Daterangepicker;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Rpj\Daterangepicker\DateHelper as Helper;

class Daterangepicker extends Filter
{
    private ?Carbon $minDate = null;
    private ?Carbon $maxDate = null;
    private ?array $ranges = null;
    private string $timezone;

    public function __construct(
        private string $column,
        private ?string $default = null,
        private string $orderByColumn = 'id',
        private string $orderByDir = 'asc',
        string $timezone = 'UTC',
    ) {
        $this->timezone = $timezone;
        $this->maxDate = Carbon::today($this->timezone);
    }

    public $component = 'daterangepicker';

    public function apply(NovaRequest $request, $query, $value): Builder
    {
        [$start, $end] = Helper::getParsedDatesGroupedRanges($value, $this->timezone);

        if ($start && $end) {
            // Преобразуем даты из часового пояса пользователя в UTC
            $startUtc = $start->setTimezone('UTC');
            $endUtc = $end->setTimezone('UTC');

            return $query->whereBetween($this->column, [$startUtc, $endUtc])
                ->orderBy($this->orderByColumn, $this->orderByDir);
        }

        return $query;
    }

    public function options(NovaRequest $request): ?array
    {
        if (!$this->ranges) {
            $this->setRanges(Helper::defaultRanges($this->timezone));
        }

        return $this->ranges;
    }

    public function default(): ?string
    {
        if ($this->default === null) {
            return null;
        }

        [$start, $end] = Helper::getParsedDatesGroupedRanges($this->default, $this->timezone);

        if ($start && $end) {
            return $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d');
        }

        return null;
    }

    public function setMinDate(Carbon $minDate): self
    {
        if ($this->maxDate && $minDate->gt($this->maxDate)) {
            throw new Exception('Date range picker: minDate must be less than or equal to maxDate.');
        }

        $this->minDate = $minDate->setTimezone($this->timezone);

        return $this;
    }

    public function setMaxDate(Carbon $maxDate): self
    {
        if ($this->minDate && $maxDate->lt($this->minDate)) {
            throw new Exception('Date range picker: maxDate must be greater than or equal to minDate.');
        }

        $this->maxDate = $maxDate->setTimezone($this->timezone);

        return $this;
    }

    public function setRanges(array $ranges): self
    {
        $this->ranges = array_map(function ($dates) {
            return array_map(fn(Carbon $date) => $date->format('Y-m-d'), $dates);
        }, $ranges);

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'minDate' => $this->minDate?->format('Y-m-d'),
            'maxDate' => $this->maxDate?->format('Y-m-d'),
            'timezone' => $this->timezone,
        ]);
    }
}
