<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

final class PriorToDateNormalizer
{
    public static function normalize(
        string|Carbon|CarbonImmutable|CarbonPeriod $date,
    ): Carbon|CarbonImmutable {
        if (is_string($date)) {
            return CarbonImmutable::parse($date);
        }

        if ($date instanceof CarbonPeriod) {
            return $date->startDate;
        }

        return $date;
    }
}
