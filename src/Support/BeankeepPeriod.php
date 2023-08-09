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
        if ($defaultPeriod = config('beankeep.default-period')) {
            [$startDateStr, $endDateStr] = $defaultPeriod;

            return CarbonImmutable::parse($startDateStr)
                ->daysUntil(CarbonImmutable::parse($endDateStr)->endOfDay());
        }

        $startOfYear = CarbonImmutable::now()->startOfYear();
        $endOfYear = CarbonImmutable::now()->endOfYear();

        return $startOfYear->daysUntil($endOfYear);
    }
}
