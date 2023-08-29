<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Journal;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\JournalPeriod;
use STS\Beankeep\Models\Journal;
use STS\Beankeep\Tests\TestCase;

final class JournalCurrentPeriodTest extends TestCase
{
    public function testCurrentPeriodRespondsWithCurrentCalendarYear(): void
    {
        $this->travelTo(Carbon::parse('08/29/2023'));

        $journal = new Journal(['period' => JournalPeriod::Jan]);

        $expectedStartDate = CarbonImmutable::parse('01/01/2023');
        $expectedEndDate = CarbonImmutable::parse('12/31/2023')->endOfDay();

        $period = $journal->currentPeriod();

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    /* TODO(zmd): move these tests (those which are applicable) into Journal's
         test of ::period()

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
    */

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
