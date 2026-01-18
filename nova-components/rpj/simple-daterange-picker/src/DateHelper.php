<?php

namespace Rpj\Daterangepicker;

use Exception;
use Illuminate\Support\Carbon;

class DateHelper
{
    const ALL = 'Все';
    const TODAY = 'Сегодня';
    const YESTERDAY = 'Вчера';
    const LAST_7_DAYS = 'Последние 7 дней';
    const THIS_WEEK = 'Эта неделя';
    const LAST_WEEK = 'Прошлая неделя';
    const LAST_30_DAYS = 'Последние 30 дней';
    const THIS_MONTH = 'Текущий месяц';
    const LAST_MONTH = 'Последний месяц';
    const LAST_6_MONTHS = 'Последние 6 месяцев';
    const THIS_YEAR = 'Этот год';

    /**
     * Возвращает диапазоны дат по умолчанию с учетом часового пояса.
     *
     * @param string $timezone
     * @return array
     */
    public static function defaultRanges(string $timezone = 'UTC'): array
    {
        $today = Carbon::today($timezone);

        return [
            self::TODAY => [$today->copy(), $today->copy()],
            self::YESTERDAY => [$today->copy()->subDay(), $today->copy()->subDay()],
            self::LAST_7_DAYS => [$today->copy()->subDays(6), $today->copy()],
            self::LAST_30_DAYS => [$today->copy()->subDays(29), $today->copy()],
            self::THIS_MONTH => [$today->copy()->startOfMonth(), $today->copy()],
            self::LAST_MONTH => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth()
            ],
            self::LAST_6_MONTHS => [
                $today->copy()->subMonths(5)->startOfMonth(),
                $today->copy()
            ],
            self::THIS_YEAR => [$today->copy()->startOfYear(), $today->copy()],
        ];
    }

    /**
     * Парсит выбранное значение и возвращает соответствующий диапазон дат с учетом часового пояса.
     *
     * @param mixed $value
     * @param string $timezone
     * @return array
     * @throws Exception
     */
    public static function getParsedDatesGroupedRanges($value, string $timezone = 'UTC'): array
    {
        if ($value === self::ALL) {
            return [null, null];
        }

        switch ($value) {
            case self::TODAY:
                $start = Carbon::today($timezone);
                $end = Carbon::today($timezone);
                break;
            case self::YESTERDAY:
                $start = Carbon::yesterday($timezone);
                $end = Carbon::yesterday($timezone);
                break;
            case self::LAST_7_DAYS:
                $start = Carbon::today($timezone)->subDays(6);
                $end = Carbon::today($timezone);
                break;
            case self::THIS_WEEK:
                $start = Carbon::now($timezone)->startOfWeek();
                $end = Carbon::now($timezone);
                break;
            case self::LAST_WEEK:
                $start = Carbon::now($timezone)->startOfWeek()->subWeek();
                $end = $start->copy()->endOfWeek();
                break;
            case self::LAST_30_DAYS:
                $start = Carbon::today($timezone)->subDays(29);
                $end = Carbon::today($timezone);
                break;
            case self::THIS_MONTH:
                $start = Carbon::now($timezone)->startOfMonth();
                $end = Carbon::now($timezone);
                break;
            case self::LAST_MONTH:
                $start = Carbon::now($timezone)->subMonth()->startOfMonth();
                $end = Carbon::now($timezone)->subMonth()->endOfMonth();
                break;
            case self::LAST_6_MONTHS:
                $start = Carbon::now($timezone)->subMonths(5)->startOfMonth();
                $end = Carbon::now($timezone);
                break;
            case self::THIS_YEAR:
                $start = Carbon::now($timezone)->startOfYear();
                $end = Carbon::now($timezone);
                break;
            default:
                $parsed = explode(' - ', $value);
                if (count($parsed) === 1) {
                    $start = Carbon::createFromFormat('Y-m-d', $value, $timezone);
                    $end = $start->copy();
                } elseif (count($parsed) === 2) {
                    $start = Carbon::createFromFormat('Y-m-d', $parsed[0], $timezone);
                    $end = Carbon::createFromFormat('Y-m-d', $parsed[1], $timezone);
                } else {
                    throw new Exception('Date range picker: некорректный формат даты.');
                }
        }

        return [
            $start->startOfDay(),
            $end->endOfDay(),
        ];
    }
}
