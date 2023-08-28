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
        $this->travelTo('2023-08-28');

        $period = JournalPeriod::Jan->toCarbonPeriod();

        $this->assertInstanceOf(CarbonPeriod::class, $period);
        $this->assertInstanceOf(CarbonImmutable::class, $period->startDate);
        $this->assertInstanceOf(CarbonImmutable::class, $period->endDate);
        $this->assertEquals(CarbonImmutable::parse('2023-01-01'), $period->startDate);
        $this->assertEquals(CarbonImmutable::parse('2023-12-31')->endOfDay(), $period->endDate);
    }

    public function testToCarbonPeriodReturnsStartDateForPriorYear(): void
    {
        $this->travelTo('2023-04-01');

        $period = JournalPeriod::May->toCarbonPeriod();

        $this->assertEquals(CarbonImmutable::parse('2022-05-01'), $period->startDate);
        $this->assertEquals(CarbonImmutable::parse('2023-04-30')->endOfDay(), $period->endDate);
    }

    // TODO(zmd): public function testToCarbonPeriodReturnsStartDateMatchingTheCurrentDate(): void {}

    // TODO(zmd): public function testToCarbonPeriodReturnsEndDateAfterTheCurrentYear(): void {}

    // TODO(zmd): public function testToCarbonPeriodReturnsEndDateMatchingTheCurrentDate(): void {}

    // TODO(zmd): public function testToCarbonPeriodReturnsCorrectDateForLeapYear(): void {}

    // -- ::expanded() --------------------------------------------------------

    // TODO(zmd): test ::expanded()

    // -- ::fromString() ------------------------------------------------------

    // TODO(zmd): test ::fromString()
}
