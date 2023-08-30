<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\LineItem;

use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
//use STS\Beankeep\Tests\TestSupport\Traits\HasTransactionMakingShortcuts;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;
use ValueError;

final class LineItemScopeTest extends TestCase
{
    //use HasTransactionMakingShortcuts;
    use GeneratesJournalData;

    public function setUp(): void
    {
        parent::setUp();

        /*
        $this->createAccountsIfMissing();

        $this->txn(     lastYear: '12/25', dr: ['cash',                10000.00], cr: ['capital',             10000.00]);
        $this->txn(     thisYear: '1/5',   dr: ['accounts-receivable',  1200.00], cr: ['services-revenue',     1200.00]);
        $this->txn(     thisYear: '1/10',  dr: ['cost-of-services',       15.00], cr: ['cash',                   15.00]);
        $this->txn(     thisYear: '1/20',  dr: ['equipment',            5000.00], cr: ['accounts-payable',     5000.00]);
        $this->txn(     thisYear: '2/1',   dr: ['rent-expense',          450.00], cr: ['cash',                  450.00]);
        $this->txn(     thisYear: '2/12',  dr: ['accounts-receivable',   240.00], cr: ['services-revenue',      240.00]);
        $this->draftTxn(thisYear: '2/16',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
        $this->draftTxn(thisYear: '2/26',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
        */

        $this->travelTo('8/30/2023');

        // TODO(zmd): make this a reality
        [$_journal, $_accounts] = $this->for('jan', function ($txn, $draft) {
            $txn(  '12/25/2022', dr: ['cash',                10000.00], cr: ['capital',             10000.00]);
            $txn(  '1/5/2023',   dr: ['accounts-receivable',  1200.00], cr: ['services-revenue',     1200.00]);
            $txn(  '1/10/2023',  dr: ['cost-of-services',       15.00], cr: ['cash',                   15.00]);
            $txn(  '1/20/2023',  dr: ['equipment',            5000.00], cr: ['accounts-payable',     5000.00]);
            $txn(  '2/1/2023',   dr: ['rent-expense',          450.00], cr: ['cash',                  450.00]);
            $txn(  '2/12/2023',  dr: ['accounts-receivable',   240.00], cr: ['services-revenue',      240.00]);
            $draft('2/16/2023',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
            $draft('2/26/2023',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
        });
    }

    // -- ::scopeLedgerEntries() ----------------------------------------------

    public function testLedgerEntriesIncludesOnlyPostedEntriesForSpecifiedPeriod(): void
    {
        $this->travelTo('5/1/2023');
        $period = JournalPeriod::Feb->toPeriod();

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
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

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
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $date = $this->getDate(thisYear: '2/1')->toMutable();

        $this->assertEquals(8, LineItem::ledgerEntries(priorTo: $date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('credit'));
    }

    public function testLedgerEntriesReturnsPostedEntriesPriorToDatePassedAsCarbonImmutable(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $date = $this->getDate(thisYear: '2/1')->toImmutable();

        $this->assertEquals(8, LineItem::ledgerEntries(priorTo: $date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $date)->sum('credit'));
    }

    public function testLedgerEntriesReturnsPostedEntriesPriorToDatePassedAsCarbonPeriod(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $start = $this->getDate(thisYear: '2/1');
        $end = $this->getDate(thisYear: '2/1')->endOfMonth();
        $period = $start->daysUntil($end);

        $this->assertEquals(8, LineItem::ledgerEntries(priorTo: $period)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $period)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntries(priorTo: $period)->sum('credit'));
    }

    public function testLedgerEntriesCarpsIfYouPassBothAPeriodAndAPriorToDate(): void
    {
        $date = $this->getDate(thisYear: '2/1');

        $this->expectException(ValueError::class);

        LineItem::ledgerEntries($date->daysUntil($date->endOfMonth()), $date);
    }

    public function testLedgerEntriesCarpsIfYouPassNeigherAPeriodNorAPriorToDate(): void
    {
        $this->expectException(ValueError::class);

        LineItem::ledgerEntries();
    }

    // -- ::scopeLedgerEntriesForPeriod() -------------------------------------

    public function testLedgerEntriesForPeriodIncludesOnlyPostedEntriesForSpecifiedPeriod(): void
    {
        $this->assertEquals(4, LineItem::ledgerEntriesForPeriod($this->febPeriod())->count());
        $this->assertEquals(69000, LineItem::ledgerEntriesForPeriod($this->febPeriod())->sum('debit'));
        $this->assertEquals(69000, LineItem::ledgerEntriesForPeriod($this->febPeriod())->sum('credit'));
    }

    // -- ::scopeLedgerEntriesPriorTo() ---------------------------------------

    public function testLedgerEntriesPriorToWithDatePassedAsString(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

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
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $date = $this->getDate(thisYear: '2/1')->toMutable();

        $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('credit'));
    }

    public function testLedgerEntriesPriorToWithDatePassedAsCarbonImmutable(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $date = $this->getDate(thisYear: '2/1')->toImmutable();

        $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($date)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($date)->sum('credit'));
    }

    public function testLedgerEntriesPriorToWithDatePassedAsCarbonPeriod(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $start = $this->getDate(thisYear: '2/1');
        $end = $this->getDate(thisYear: '2/1')->endOfMonth();
        $period = $start->daysUntil($end);

        $this->assertEquals(8, LineItem::ledgerEntriesPriorTo($period)->count());
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($period)->sum('debit'));
        $this->assertEquals(1621500, LineItem::ledgerEntriesPriorTo($period)->sum('credit'));
    }

    // -- ::scopePeriod() -----------------------------------------------------

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
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);
        $date = $this->getDate(thisYear: '2/1');

        $this->assertEquals(10, LineItem::priorTo($date)->count());
        $this->assertEquals(1626500, LineItem::priorTo($date)->sum('debit'));
        $this->assertEquals(1626500, LineItem::priorTo($date)->sum('credit'));
    }

    // -- ::scopePosted() -----------------------------------------------------

    public function testScopePostedReturnsJustPostedLineItems(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(12, LineItem::posted()->count());
        $this->assertEquals(1690500, LineItem::posted()->sum('debit'));
        $this->assertEquals(1690500, LineItem::posted()->sum('credit'));
    }

    // -- ::scopePending() ----------------------------------------------------

    public function testScopePendingJustReturnsPendingLineItems(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(6, LineItem::pending()->count());
        $this->assertEquals(553000, LineItem::pending()->sum('debit'));
        $this->assertEquals(553000, LineItem::pending()->sum('credit'));
    }

    // -- ::scopeDebits() -----------------------------------------------------

    public function testScopeDebitsJustReturnsDebitLineItems(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(9, LineItem::debits()->count());
        $this->assertEquals(2243500, LineItem::debits()->sum('debit'));
        $this->assertEquals(0, LineItem::debits()->sum('credit'));
    }

    // -- ::scopeCredits() ----------------------------------------------------

    public function testScopeCreditsJustReturnsDebitLineItems(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(9, LineItem::credits()->count());
        $this->assertEquals(0, LineItem::credits()->sum('debit'));
        $this->assertEquals(2243500, LineItem::credits()->sum('credit'));
    }
}
