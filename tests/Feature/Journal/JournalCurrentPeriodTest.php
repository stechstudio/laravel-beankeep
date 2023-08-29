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

    public function testCurrentPeriodAccountsForCurrentDateToDetermineStartDate(): void
    {
        $this->travelTo(Carbon::parse('5/4/2023'));

        $journal = new Journal(['period' => JournalPeriod::Dec]);
        $expectedStartDate = CarbonImmutable::parse('12/1/2022');
        $expectedEndDate = CarbonImmutable::parse('11/30/2023')->endOfDay();

        $period = $journal->currentPeriod();

        $this->assertEquals($expectedStartDate, $period->startDate);
        $this->assertEquals($expectedEndDate, $period->endDate);
    }
}
