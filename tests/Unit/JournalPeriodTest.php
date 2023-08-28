<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use STS\Beankeep\Enums\JournalPeriod;

final class JournalPeriodTest extends TestCase
{
    use InteractsWithTime;

    // -- ::toCarbonPeriod() --------------------------------------------------

    public function testToCarbonPeriod(): void
    {
        // TODO(zmd): we need to use time travel to ensure we have a consistent
        //   frame of reference
        $this->travelTo('2023-08-28');

        $janPeriod = JournalPeriod::Jan->toCarbonPeriod();

        $this->assertInstanceOf(CarbonPeriod::class, $janPeriod);
        $this->assertInstanceOf(CarbonImmutable::class, $janPeriod->startDate);
        $this->assertInstanceOf(CarbonImmutable::class, $janPeriod->endDate);
        $this->assertEquals(CarbonImmutable::parse('2023-01-01'), $janPeriod->startDate);
        $this->assertEquals(CarbonImmutable::parse('2023-12-31')->endOfDay(), $janPeriod->endDate);
    }

    // TODO(zmd): test that start date is always before or same as current date

    // TODO(zmd): test that end date is always after or same as current date

    // TODO(zmd): test that leap-year as expected when end date is 29-feb

    // -- ::expanded() --------------------------------------------------------

    // TODO(zmd): test ::expanded()

    // -- ::fromString() ------------------------------------------------------

    // TODO(zmd): test ::fromString()
}
