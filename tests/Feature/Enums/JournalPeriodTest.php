<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Enums;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\JournalPeriod;
use STS\Beankeep\Tests\TestCase;

final class JournalPeriodTest extends TestCase
{
    // -- ::get() -------------------------------------------------------------

    public function testGetWithCarbonPeriodReturnsThatPeriod(): void
    {
        $startDate = CarbonImmutable::now()->startOfMonth();
        $endDate = CarbonImmutable::now()->endOfMonth();
        $expectedPeriod = $startDate->daysUntil($endDate);

        $period = JournalPeriod::get($expectedPeriod);

        $this->assertEquals($expectedPeriod, $period);
    }

    public function testGetWithNullFallsBackToCurrentCalendarYearInAbsenceOfConfig(): void
    {
        $expectedStartDate = CarbonImmutable::parse('1/1');
        $expectedEndDate = $expectedStartDate->endOfYear();

        $period = JournalPeriod::get(null);

        $this->assertNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testGetWithNullFallsBackToConfiguredPeriodWhenAvailable(): void
    {
        $this->travelTo(Carbon::parse('11/23/2023'));
        config(['beankeep.default-period' => 'oct']);

        $expectedStartDate = CarbonImmutable::parse(
            '1-oct ' . $this->thisYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '30-sep ' . $this->nextYear(),
        )->endOfDay();

        $period = JournalPeriod::get(null);

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    // -- ::defaultPeriod() ---------------------------------------------------

    public function testDefaultPeriodRespondsWithCurrentCalendarYearInAbsenceOfConfig(): void
    {
        $expectedStartDate = CarbonImmutable::parse('1/1');
        $expectedEndDate = $expectedStartDate->endOfYear();

        $period = JournalPeriod::defaultPeriod();

        $this->assertNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testDefaultPeriodRespondsWithConfiguredPeriodWhenAvailable(): void
    {
        $this->travelTo(Carbon::parse('11/23/2023'));
        config(['beankeep.default-period' => 'oct']);

        $expectedStartDate = CarbonImmutable::parse(
            '1-oct ' . $this->thisYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '30-sep ' . $this->nextYear(),
        )->endOfDay();

        $period = JournalPeriod::defaultPeriod();

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testDefaultPeriodDealsWithLeapYearsForConfiguredEndPeriodBeingFeb(): void
    {
        $this->travelTo(Carbon::parse('5/4/2023'));
        config(['beankeep.default-period' => 'mar']);

        $expectedStartDate = CarbonImmutable::parse(
            '1-mar ' . $this->thisYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '29-feb ' . $this->nextYear(),
        )->endOfDay();

        $period = JournalPeriod::defaultPeriod();

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testDefaultPeriodCorrectlyHandlesContextOfCurrentTime(): void
    {
        $this->travelTo(Carbon::parse('5/4/2023'));
        config(['beankeep.default-period' => 'dec']);

        $expectedStartDate = CarbonImmutable::parse(
            '1-dec ' . $this->lastYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '30-nov ' . $this->thisYear(),
        )->endOfDay();

        $period = JournalPeriod::defaultPeriod();

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    // ========================================================================

    protected function lastYear(): string
    {
        return Carbon::now()->subYear()->format('Y');
    }

    protected function thisYear(): string
    {
        return Carbon::now()->format('Y');
    }

    protected function nextYear(): string
    {
        return Carbon::now()->addYear()->format('Y');
    }
}
