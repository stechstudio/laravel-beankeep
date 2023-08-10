<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\LineItem;

use Illuminate\Support\Carbon;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class LineItemScopeTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
    }

    // -- ::scopeLedgerEntries() ----------------------------------------------

    public function testLedgerEntriesIncludesOnlyPostedEntriesForDefaultPeriodWhenPassedNoArguments(): void
    {
        $this->assertEquals(10, LineItem::ledgerEntries()->count());
        $this->assertEquals(690500, LineItem::ledgerEntries()->sum('debit'));
        $this->assertEquals(690500, LineItem::ledgerEntries()->sum('credit'));
    }

    public function testLedgerEntriesIncludesOnlyPostedEntriesForConfiguredDefaultPeriodWhenPassedNoArguments(): void
    {
        $this->travelTo($this->getDate(thisYear: '5/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(12, LineItem::ledgerEntries()->count());
        $this->assertEquals(1690500, LineItem::ledgerEntries()->sum('debit'));
        $this->assertEquals(1690500, LineItem::ledgerEntries()->sum('credit'));
    }

    public function testLedgerEntriesIncludesOnlyPostedEntriesForSpecifiedPeriod(): void
    {
        $this->assertEquals(4, LineItem::ledgerEntries($this->febPeriod())->count());
        $this->assertEquals(69000, LineItem::ledgerEntries($this->febPeriod())->sum('debit'));
        $this->assertEquals(69000, LineItem::ledgerEntries($this->febPeriod())->sum('credit'));
    }

    public function testLedgerEntriesIncludesOnlyPostedEntriesForSpecifiedPeriodPassedAsKeywordArgument(): void
    {
        $this->assertEquals(4, LineItem::ledgerEntries(period: $this->febPeriod())->count());
        $this->assertEquals(69000, LineItem::ledgerEntries(period: $this->febPeriod())->sum('debit'));
        $this->assertEquals(69000, LineItem::ledgerEntries(period: $this->febPeriod())->sum('credit'));
    }

    public function testLedgerEntriesReturnsPostedEntriesPriorToDatePassedAsString(): void
    {
        $this->unpostedTransactionLastYear();

        foreach ([
            $this->getDate(thisYear: '2/1')->format('d-M Y'),
            $this->getDate(thisYear: '2/1')->format('Y-m-d'),
            $this->getDate(thisYear: '2/1')->format('m/d/Y'),
        ] as $dateStr) {
            $this->assertEquals(8, LineItem::ledgerEntries(priorTo: $dateStr)->count());
            $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $dateStr)->sum('debit'));
            $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $dateStr)->sum('credit'));
        }
    }

    public function testLedgerEntriesReturnsPostedEntriesPriorToDatePassedAsCarbon(): void
    {
        $this->unpostedTransactionLastYear();
        $date = $this->getDate(thisYear: '2/1')->toMutable();

        $this->assertEquals(8, LineItem::ledgerEntries(priorTo: $date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('credit'));
    }

    public function testLedgerEntriesReturnsPostedEntriesPriorToDatePassedAsCarbonImmutable(): void
    {
        $this->unpostedTransactionLastYear();
        $date = $this->getDate(thisYear: '2/1')->toImmutable();

        $this->assertEquals(8, LineItem::ledgerEntries(priorTo: $date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('credit'));
    }

    // TODO(zmd): public function testLedgerEntriesReturnsPostedEntriesPriorToDatePassedAsCarbonPeriod(): void {}

    // TODO(zmd): public function testLedgerEntriesCarpsIfYouPassBothAPeriodAndAPriorToDate(): void {}

    // -- ::scopeLedgerEntriesForPeriod() -------------------------------------

    public function testLedgerEntriesForPeriodIncludesOnlyPostedEntriesForDefaultPeriod(): void
    {
        $this->assertEquals(10, LineItem::ledgerEntriesForPeriod()->count());
        $this->assertEquals(690500, LineItem::ledgerEntriesForPeriod()->sum('debit'));
        $this->assertEquals(690500, LineItem::ledgerEntriesForPeriod()->sum('credit'));
    }

    public function testLedgerEntriesForPeriodIncludesOnlyPostedEntriesForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->getDate(thisYear: '5/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(12, LineItem::ledgerEntriesForPeriod()->count());
        $this->assertEquals(1690500, LineItem::ledgerEntriesForPeriod()->sum('debit'));
        $this->assertEquals(1690500, LineItem::ledgerEntriesForPeriod()->sum('credit'));
    }

    public function testLedgerEntriesForPeriodIncludesOnlyPostedEntriesForSpecifiedPeriod(): void
    {
        $this->assertEquals(4, LineItem::ledgerEntriesForPeriod($this->febPeriod())->count());
        $this->assertEquals(69000, LineItem::ledgerEntriesForPeriod($this->febPeriod())->sum('debit'));
        $this->assertEquals(69000, LineItem::ledgerEntriesForPeriod($this->febPeriod())->sum('credit'));
    }

    // -- ::scopeLedgerEntriesPriorTo() ---------------------------------------

    public function testLedgerEntriesPriorToWithDatePassedAsString(): void
    {
        $this->unpostedTransactionLastYear();

        foreach ([
            $this->getDate(thisYear: '2/1')->format('d-M Y'),
            $this->getDate(thisYear: '2/1')->format('Y-m-d'),
            $this->getDate(thisYear: '2/1')->format('m/d/Y'),
        ] as $dateStr) {
            $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($dateStr)->count());
            $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($dateStr)->sum('debit'));
            $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($dateStr)->sum('credit'));
        }
    }

    public function testLedgerEntriesPriorToWithDatePassedAsCarbon(): void
    {
        $this->unpostedTransactionLastYear();
        $date = $this->getDate(thisYear: '2/1')->toMutable();

        $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('credit'));
    }

    public function testLedgerEntriesPriorToWithDatePassedAsCarbonImmutable(): void
    {
        $this->unpostedTransactionLastYear();
        $date = $this->getDate(thisYear: '2/1')->toImmutable();

        $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('credit'));
    }

    public function testLedgerEntriesPriorToWithDatePassedAsCarbonPeriod(): void
    {
        $this->unpostedTransactionLastYear();
        $start = $this->getDate(thisYear: '2/1');
        $end = $this->getDate(thisYear: '2/1')->endOfMonth();
        $period = $start->daysUntil($end);

        $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($period)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($period)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($period)->sum('credit'));
    }

    // -- ::scopePeriod() -----------------------------------------------------

    public function testPeriodIncludesAllItemsForDefaultPeriod(): void
    {
        $this->assertEquals(14, LineItem::period()->count());
        $this->assertEquals(1238500, LineItem::period()->sum('debit'));
        $this->assertEquals(1238500, LineItem::period()->sum('credit'));
    }

    public function testPeriodIncludesAllItemsForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->getDate(thisYear: '5/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(16, LineItem::period()->count());
        $this->assertEquals(2238500, LineItem::period()->sum('debit'));
        $this->assertEquals(2238500, LineItem::period()->sum('credit'));
    }

    public function testPeriodIncludesAllItemsForSpecifiedPeriod(): void
    {
        $this->assertEquals(8, LineItem::period($this->febPeriod())->count());
        $this->assertEquals(617000, LineItem::period($this->febPeriod())->sum('debit'));
        $this->assertEquals(617000, LineItem::period($this->febPeriod())->sum('credit'));
    }

    // -- ::scopePriorTo() ----------------------------------------------------

    public function testPriorToWithDatePassedAsString(): void
    {
        foreach ([
            $this->getDate(thisYear: '2/1')->format('d-M Y'),
            $this->getDate(thisYear: '2/1')->format('Y-m-d'),
            $this->getDate(thisYear: '2/1')->format('m/d/Y'),
        ] as $dateStr) {
            $this->assertEquals(8, LineItem::priorTo($dateStr)->count());
            $this->assertEquals(1621500, LineItem::priorTo($dateStr)->sum('debit'));
            $this->assertEquals(1621500, LineItem::priorTo($dateStr)->sum('credit'));
        }
    }

    public function testPriorToWithDatePassedAsCarbon(): void
    {
        $date = $this->getDate(thisYear: '2/1')->toMutable();

        $this->assertEquals(8, LineItem::priorTo($date)->count());
        $this->assertEquals(1621500, LineItem::priorTo($date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::priorTo($date)->sum('credit'));
    }

    public function testPriorToWithDatePassedAsCarbonImmutable(): void
    {
        $date = $this->getDate(thisYear: '2/1')->toImmutable();

        $this->assertEquals(8, LineItem::priorTo($date)->count());
        $this->assertEquals(1621500, LineItem::priorTo($date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::priorTo($date)->sum('credit'));
    }

    public function testPriorToWithDatePassedAsCarbonPeriod(): void
    {
        $start = $this->getDate(thisYear: '2/1');
        $end = $this->getDate(thisYear: '2/1')->endOfMonth();
        $period = $start->daysUntil($end);

        $this->assertEquals(8, LineItem::priorTo($period)->count());
        $this->assertEquals(1621500, LineItem::priorTo($period)->sum('debit'));
        $this->assertEquals(1621500, LineItem::priorTo($period)->sum('credit'));
    }

    public function testPriorToDoesNotFilterOutPendingItems(): void
    {
        $this->unpostedTransactionLastYear();
        $date = $this->getDate(thisYear: '2/1');

        $this->assertEquals(10, LineItem::priorTo($date)->count());
        $this->assertEquals(1626500, LineItem::priorTo($date)->sum('debit'));
        $this->assertEquals(1626500, LineItem::priorTo($date)->sum('credit'));
    }

    // -- ::scopePosted() -----------------------------------------------------

    // -- ::scopePending() ----------------------------------------------------

    // -- ::scopeDebits() -----------------------------------------------------

    // -- ::scopeCredits() ----------------------------------------------------
}
