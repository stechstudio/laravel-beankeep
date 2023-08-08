<?php

declare(strict_types=1)

use Carbon\CarbonPeriod;

final class BeankeepPeriod
{
    public static function from(?iterable $period): CarbonPeriod
    {
        if (is_null($period)) {
            return self::defaultPeriod();
        } elseif ($period instanceof CarbonPeriod::class) {
            return $period;
        }

        // TODO(zmd): finish implementing me
    }

    public static function defaultPeriod(): CarbonPeriod
    {
        [$startDateStr, $endDateStr] = config('beankeep.default-period');

        return Carbon::parse($startDateStr)->daysUntil($endDateStr);
    }
}
