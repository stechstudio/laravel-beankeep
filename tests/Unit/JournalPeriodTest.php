<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use PHPUnit\Framework\TestCase;
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

    public function testToCarbonPeriodReturnsStartDateMatchingTheCurrentDate(): void
    {
        $this->travelTo('2023-01-01');

        $period = JournalPeriod::Jan->toCarbonPeriod();

        $this->assertEquals(CarbonImmutable::parse('2023-01-01'), $period->startDate);
        $this->assertEquals(CarbonImmutable::parse('2023-12-31')->endOfDay(), $period->endDate);
    }

    public function testToCarbonPeriodReturnsEndDateAfterTheCurrentYear(): void
    {
        $this->travelTo('2023-06-03');

        $period = JournalPeriod::May->toCarbonPeriod();

        $this->assertEquals(CarbonImmutable::parse('2023-05-01'), $period->startDate);
        $this->assertEquals(CarbonImmutable::parse('2024-04-30')->endOfDay(), $period->endDate);
    }

    public function testToCarbonPeriodReturnsEndDateMatchingTheCurrentDate(): void
    {
        $this->travelTo('2023-12-31');

        $period = JournalPeriod::Jan->toCarbonPeriod();

        $this->assertEquals(CarbonImmutable::parse('2023-01-01'), $period->startDate);
        $this->assertEquals(CarbonImmutable::parse('2023-12-31')->endOfDay(), $period->endDate);
    }

    public function testToCarbonPeriodReturnsCorrectDateForLeapYear(): void
    {
        $this->travelTo('2024-01-21');

        $period = JournalPeriod::March->toCarbonPeriod();

        $this->assertEquals(CarbonImmutable::parse('2023-03-01'), $period->startDate);
        $this->assertEquals(CarbonImmutable::parse('2024-02-29')->endOfDay(), $period->endDate);
    }

    // -- ::expanded() --------------------------------------------------------

    public function testExpandedReturnsFullStringCorrespondingToMonthValue(): void
    {
        $this->assertEquals('January', JournalPeriod::Jan->expanded());
        $this->assertEquals('February', JournalPeriod::Feb->expanded());
        $this->assertEquals('March', JournalPeriod::March->expanded());
        $this->assertEquals('April', JournalPeriod::April->expanded());
        $this->assertEquals('May', JournalPeriod::May->expanded());
        $this->assertEquals('June', JournalPeriod::June->expanded());
        $this->assertEquals('July', JournalPeriod::July->expanded());
        $this->assertEquals('August', JournalPeriod::Aug->expanded());
        $this->assertEquals('September', JournalPeriod::Sept->expanded());
        $this->assertEquals('October', JournalPeriod::Oct->expanded());
        $this->assertEquals('November', JournalPeriod::Nov->expanded());
        $this->assertEquals('December', JournalPeriod::Dec->expanded());
    }

    // -- ::fromString() ------------------------------------------------------

    // TODO(zmd): test ::fromString()
}
