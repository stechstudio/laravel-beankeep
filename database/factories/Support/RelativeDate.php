<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

final class RelativeDate
{
    public readonly RelativeToYear $thisYear;

    public readonly RelativeToYear $lastYear;

    private CarbonPeriod $thisYearRange;

    private CarbonPeriod $lastYearRange;

    public function __construct()
    {
        $this->thisYear = new RelativeToYear(
            CarbonImmutable::now()->startOfYear(),
        );

        $this->lastYear = new RelativeToYear(
            CarbonImmutable::now()->startOfYear()->subYear(),
        );
    }

    public function thisYearRange(): CarbonPeriod
    {
        return $this->thisYearRange ??= $this->thisYear['1/1']
            ->daysUntil($this->thisYear['12/31']);
    }

    public function lastYearRange(): CarbonPeriod
    {
        return $this->lastYearRange ??= $this->lastYear['1/1']
            ->daysUntil($this->lastYear['12/31']);
    }
}
