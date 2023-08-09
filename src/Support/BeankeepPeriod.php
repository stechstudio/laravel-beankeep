<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

final class BeankeepPeriod
{
    public static function from(?CarbonPeriod $period): CarbonPeriod
    {
        if (is_null($period)) {
            return self::defaultPeriod();
        }

        return $period;
    }

    public static function defaultPeriod(): CarbonPeriod
    {
        if (config('beankeep.default-period')) {
            return self::defaultPeriodFromConfig();
        }

        return self::defaultPeriodThisYear();
    }

    private static function defaultPeriodFromConfig(): CarbonPeriod {
        [$startDateStr, $endDateStr] = config('beankeep.default-period');

        $startDate = CarbonImmutable::parse($startDateStr);
        $endDate = CarbonImmutable::parse($endDateStr)->endOfDay();

        if ($startDate->greaterThan($endDate)) {
            $endDate = $endDate->addYear();
        }

        if (static::endOfFeb($endDate)) {
            $endDate = $endDate->endOfMonth();
        }

        return $startDate->daysUntil($endDate);
    }

    private static function defaultPeriodThisYear(): CarbonPeriod
    {
        $startOfYear = CarbonImmutable::now()->startOfYear();
        $endOfYear = CarbonImmutable::now()->endOfYear();

        return $startOfYear->daysUntil($endOfYear);
    }

    private static function endOfFeb(CarbonImmutable $endDate): bool
    {
        if ($endDate->month == 2) {
            return $endDate->day >= 28;
        }

        return false;
    }
}
