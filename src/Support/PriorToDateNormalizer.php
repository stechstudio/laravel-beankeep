<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

final class PriorToDateNormalizer
{
    // TODO(zmd): test me
    public static function normalize(
        string|Carbon|CarbonImmutable|CarbonPeriod $date,
    ): Carbon|CarbonImmutable {
        if ($date instanceof CarbonPeriod) {
            return $date->startDate;
        }

        return CarbonImmutable::parse($date);
    }
}
