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

    public function testCurrentPeriodHandlesLeapYearsCorrectly(): void
    {
        $this->travelTo(Carbon::parse('5/4/2023'));

        $journal = new Journal(['period' => JournalPeriod::March]);
        $expectedStartDate = CarbonImmutable::parse('3/1/2023');
        $expectedEndDate = CarbonImmutable::parse('2/29/2024')->endOfDay();

        $period = $journal->currentPeriod();

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }

    /* TODO(zmd): move these tests (those which are applicable) into Journal's
         test of ::period()
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
}
