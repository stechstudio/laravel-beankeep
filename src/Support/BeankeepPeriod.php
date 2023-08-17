<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

final class BeankeepPeriod
{
    // TODO(zmd): port to JournalPeriod
    public static function from(?CarbonPeriod $period): CarbonPeriod
    {
        if (is_null($period)) {
            return self::defaultPeriod();
        }

        return $period;
    }

    // TODO(zmd): port to JournalPeriod
    public static function defaultPeriod(): CarbonPeriod
    {
        if (config('beankeep.default-period')) {
            return self::defaultPeriodFromConfig();
        }

        return self::defaultPeriodThisYear();
    }

    // TODO(zmd): port to JournalPeriod
    private static function defaultPeriodFromConfig(): CarbonPeriod {
        [$startDateStr, $endDateStr] = config('beankeep.default-period');

        $startDate = CarbonImmutable::parse($startDateStr);
        if ($startDate->greaterThan(Carbon::now())) {
            $startDate = $startDate->subYear();
        }

        $endDate = CarbonImmutable::parse($endDateStr)->endOfDay();
        if ($startDate->greaterThan($endDate)) {
            $endDate = $endDate->addYear();
        }

        if (static::endOfFeb($endDate)) {
            $endDate = $endDate->endOfMonth();
        }

        return $startDate->daysUntil($endDate);
    }

    // TODO(zmd): port to JournalPeriod
    private static function defaultPeriodThisYear(): CarbonPeriod
    {
        $startOfYear = CarbonImmutable::now()->startOfYear();
        $endOfYear = CarbonImmutable::now()->endOfYear();

        return $startOfYear->daysUntil($endOfYear);
    }

    // TODO(zmd): port to JournalPeriod (if we keep, which we probably won't)
    private static function endOfFeb(CarbonImmutable $endDate): bool
    {
        if ($endDate->month == 2) {
            return $endDate->day >= 28;
        }

        return false;
    }
}
