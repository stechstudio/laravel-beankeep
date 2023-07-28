<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use Carbon\CarbonImmutable;

final class RelativeDate
{
    public readonly RelativeToYear $thisYear;

    public readonly RelativeToYear $lastYear;

    public function __construct()
    {
        $this->thisYear = new RelativeToYear(CarbonImmutable::now()->startOfYear());
        $this->lastYear = new RelativeToYear(CarbonImmutable::now()->startOfYear()->subYear());
    }
}
