<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Carbon\CarbonPeriod;
use STS\Beankeep\Enums\JournalPeriod;

// TODO(zmd): replace all uses with JournalPeriod then delete
final class BeankeepPeriod
{
    public static function from(?CarbonPeriod $period): CarbonPeriod
    {
        return JournalPeriod::get($period);
    }

    public static function defaultPeriod(): CarbonPeriod
    {
        return JournalPeriod::defaultPeriod();
    }
}
