<?php

namespace Devcraft\DatetimeWoTimezone;

use Laravel\Nova\Fields\DateTime;

class DatetimeWoTimezone extends DateTime
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'datetime-wo-timezone';
}
