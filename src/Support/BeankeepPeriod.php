<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

final class BeankeepPeriod
{
    // TODO(zmd): test me
    public static function from(?CarbonPeriod $period): CarbonPeriod
    {
        if (is_null($period)) {
            return self::defaultPeriod();
        }

        return $period;
    }

    // TODO(zmd): test me
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

        return $startDate->daysUntil($endDate);
    }

    private static function defaultPeriodThisYear(): CarbonPeriod
    {
        $startOfYear = CarbonImmutable::now()->startOfYear();
        $endOfYear = CarbonImmutable::now()->endOfYear();

        return $startOfYear->daysUntil($endOfYear);
    }
}
