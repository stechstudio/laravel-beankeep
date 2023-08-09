<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Support;

use Carbon\CarbonImmutable;
use STS\Beankeep\Support\BeankeepPeriod;
use STS\Beankeep\Tests\TestCase;

final class BeankeepPeriodTest extends TestCase
{
    public function testDefaultPeriodRespondsWithCurrentCalendarYearInAbsenceOfConfig(): void
    {
        $expectedStartDate = CarbonImmutable::parse('1/1');
        $expectedEndDate = $expectedStartDate->endOfYear();

        $period = BeankeepPeriod::defaultPeriod();

        $this->assertNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }
}
