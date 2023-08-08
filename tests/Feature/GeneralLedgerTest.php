<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Carbon\CarbonPeriod;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class GeneralLedgerTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
    }

    // NOTE(zmd): this is basically just a smoke test, making sure things are
    //   hooked up
    public function testItCanRecordATransactionToTheJournal(): void
    {
        $transaction = $this->thisYear('1/1')->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->doc('contribution-moa.pdf')
            ->draft();

        $this->assertEquals('initial owner contribution', $transaction->memo);
        $this->assertEquals($this->getDate(thisYear: '1/1'), $transaction->date);
        $this->assertFalse($transaction->posted);

        $this->assertEquals(2, $transaction->lineItems()->count());
        $this->assertEquals(1000000, $transaction->lineItems[0]->debit);
        $this->assertEquals($this->account('cash'), $transaction->lineItems[0]->account);
        $this->assertEquals(1000000, $transaction->lineItems[1]->credit);
        $this->assertEquals($this->account('capital'), $transaction->lineItems[1]->account);

        $this->assertEquals(1, $transaction->sourceDocuments()->count());
    }

    public function testItCanModelAJournalWithManyTransactions(): void
    {
        // NOTE(zmd): later we'll *also* check individual account balances here,
        //   once we have created helpers for doing such in the package.
        $this->assertEquals(0, LineItem::sum('debit') - LineItem::sum('credit'));
    }

    public function testItCanDifferentiateBetweenPostedAndUnpostedLineItems(): void
    {
        $this->assertEquals(12, LineItem::posted()->count());
        $this->assertEquals(0, LineItem::posted()->sum('debit') - LineItem::posted()->sum('credit'));

        $this->assertEquals(4, LineItem::pending()->count());
        $this->assertEquals(0, LineItem::pending()->sum('debit') - LineItem::pending()->sum('credit'));
    }

    public function testItCanEasilyOfferAccessToAllLineItemsWithinASpecifiedPeriod(): void
    {
        $this->assertEquals(6, LineItem::period($this->janPeriod())->count());
        $this->assertEquals(0, LineItem::period($this->janPeriod())->sum('debit') - LineItem::period($this->janPeriod())->sum('credit'));

        $this->assertEquals(8, LineItem::period($this->febPeriod())->count());
        $this->assertEquals(0, LineItem::period($this->febPeriod())->sum('debit') - LineItem::period($this->febPeriod())->sum('credit'));
    }

    // NOTE(zmd): right now the default period is the current calendar year; we
    //   will need to update this test once we make the default period
    //   user-configurable
    public function testItCanEasilyOfferAccessToAllLineItemsWithinTheDefaultPeriod(): void
    {
        $this->assertEquals(14, LineItem::period()->count());
        $this->assertEquals(0, LineItem::period()->sum('debit') - LineItem::period()->sum('credit'));
    }

    public function testItCanEasilyOfferAccessToTheGeneralLedgerWithinASpecifiedPeriod(): void
    {
        $this->assertEquals(6, LineItem::ledgerEntries($this->janPeriod())->count());
        $this->assertEquals(0, LineItem::ledgerEntries($this->janPeriod())->sum('debit') - LineItem::ledgerEntries($this->janPeriod())->sum('credit'));

        $this->assertEquals(4, LineItem::ledgerEntries($this->febPeriod())->count());
        $this->assertEquals(0, LineItem::ledgerEntries($this->febPeriod())->sum('debit') - LineItem::ledgerEntries($this->febPeriod())->sum('credit'));
    }

    // NOTE(zmd): right now the default period is the current calendar year; we
    //   will need to update this test once we make the default period
    //   user-configurable
    public function testItCanEasilyOfferAccessToTheGeneralLedgerWithinTheDefaultPeriod(): void
    {
        $this->assertEquals(10, LineItem::ledgerEntries()->count());
        $this->assertEquals(0, LineItem::ledgerEntries()->sum('debit') - LineItem::ledgerEntries()->sum('credit'));
    }

    public function testItCanGetAllDebitsThatExistInTheSystem(): void
    {
        $this->assertEquals(8, LineItem::debits()->count());
    }

    public function testItCanGetAllCreditsThatExistInTheSystem(): void
    {
        $this->assertEquals(8, LineItem::credits()->count());
    }
}
