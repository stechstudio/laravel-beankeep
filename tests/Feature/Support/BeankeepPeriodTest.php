<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use STS\Beankeep\Support\BeankeepPeriod;
use STS\Beankeep\Tests\TestCase;

final class BeankeepPeriodTest extends TestCase
{
    // -- ::from() -----------------------------------------------------------

    public function testFromWithNullFallsBackToCurrentCalendarYearInAbsenceOfConfig(): void
    {
        $expectedStartDate = CarbonImmutable::parse('1/1');
        $expectedEndDate = $expectedStartDate->endOfYear();

        $period = BeankeepPeriod::from(null);

        $this->assertNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testFromWithNullFallsBackToConfiguredPeriodWhenAvailable(): void
    {
        config(['beankeep.default-period' => ['1-oct', '30-sep']]);

        $expectedStartDate = CarbonImmutable::parse(
            '1-oct ' . $this->thisYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '30-sep ' . $this->nextYear(),
        )->endOfDay();

        $period = BeankeepPeriod::from(null);

        $this->assertNotNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    // -- ::defaultPeriod() --------------------------------------------------

    public function testDefaultPeriodRespondsWithCurrentCalendarYearInAbsenceOfConfig(): void
    {
        $expectedStartDate = CarbonImmutable::parse('1/1');
        $expectedEndDate = $expectedStartDate->endOfYear();

        $period = BeankeepPeriod::defaultPeriod();

        $this->assertNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testDefaultPeriodRespondsWithConfiguredPeriodWhenAvailable(): void
    {
        config(['beankeep.default-period' => ['1-oct', '30-sep']]);

        $expectedStartDate = CarbonImmutable::parse(
            '1-oct ' . $this->thisYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '30-sep ' . $this->nextYear(),
        )->endOfDay();

        $period = BeankeepPeriod::defaultPeriod();

        $this->assertNotNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    public function testDefaultPeriodDealsWithLeapYearsForConfiguredEndPeriodBeingFeb(): void
    {
        $this->travelTo(Carbon::parse('5/4/2023'));
        config(['beankeep.default-period' => ['1-mar', '28-feb']]);

        $expectedStartDate = CarbonImmutable::parse(
            '1-mar ' . $this->thisYear(),
        );

        $expectedEndDate = CarbonImmutable::parse(
            '29-feb ' . $this->nextYear(),
        )->endOfDay();

        $period = BeankeepPeriod::defaultPeriod();

        $this->assertNotNull(config('beankeep.default-period'));
        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    // =======================================================================

    protected function thisYear(): string
    {
        return Carbon::now()->format('Y');
    }

    protected function nextYear(): string
    {
        return Carbon::now()->addYear()->format('Y');
    }
}
