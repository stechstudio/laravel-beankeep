<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Carbon\CarbonPeriod;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\CanCreateAccounts;

final class GeneralLedgerTest extends TestCase
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    public function setUp(): void
    {
        parent::setUp();
        $this->createAccounts();
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
        $this->twoMonthsOfTransactions();

        // NOTE(zmd): later we'll *also* check individual account balances here,
        //   once we have created helpers for doing such in the package.
        $this->assertEquals(0, LineItem::sum('debit') - LineItem::sum('credit'));
    }

    public function testItCanDifferentiateBetweenPostedAndUnpostedLineItems(): void
    {
        $this->twoMonthsOfTransactions();

        $this->assertEquals(10, LineItem::posted()->count());
        $this->assertEquals(0, LineItem::posted()->sum('debit') - LineItem::posted()->sum('credit'));

        $this->assertEquals(4, LineItem::pending()->count());
        $this->assertEquals(0, LineItem::pending()->sum('debit') - LineItem::pending()->sum('credit'));
    }

    public function testItCanEasilyOfferAccessToAllLineItemsWithinASpecifiedPeriod(): void
    {
        $this->twoMonthsOfTransactions();

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
        $this->twoMonthsOfTransactions();

        $this->assertEquals(14, LineItem::period()->count());
        $this->assertEquals(0, LineItem::period()->sum('debit') - LineItem::period()->sum('credit'));
    }

    public function testItCanEasilyOfferAccessToTheGeneralLedgerWithinASpecifiedPeriod(): void
    {
        $this->twoMonthsOfTransactions();

        $this->assertEquals(6, LineItem::ledger($this->janPeriod())->count());
        $this->assertEquals(0, LineItem::ledger($this->janPeriod())->sum('debit') - LineItem::ledger($this->janPeriod())->sum('credit'));

        $this->assertEquals(4, LineItem::ledger($this->febPeriod())->count());
        $this->assertEquals(0, LineItem::ledger($this->febPeriod())->sum('debit') - LineItem::ledger($this->febPeriod())->sum('credit'));
    }

    // =======================================================================

    protected function janPeriod(): CarbonPeriod
    {
        $start = $this->getDate(thisYear: '1/1');
        $end = $start->endOfMonth();

        return $start->daysUntil($end);
    }

    protected function febPeriod(): CarbonPeriod
    {
        $start = $this->getDate(thisYear: '2/1');
        $end = $start->endOfMonth();

        return $start->dayUntil($end);
    }

    // TODO(zmd): we need to make this 3 months, and include the prior year (so
    //   that our "default period" tests are testing something more meaningful)
    protected function twoMonthsOfTransactions(): void
    {
        $this->thisYear('1/1')
            ->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->doc('contribution-moa.pdf')
            ->post();

        $this->thisYear('1/10')
            ->transact('register domain')
            ->line('cost-of-services', dr: 15.00)
            ->line('cash', cr: 15.00)
            ->doc('namecheap-receipt.pdf')
            ->post();

        $this->thisYear('1/20')
            ->transact('2 computers from computers-ᴙ-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->doc('computers-ᴙ-us-receipt.pdf')
            ->post();

        $this->thisYear('2/1')
            ->transact("pay office space rent - feb")
            ->line('rent-expense', dr: 450.00)
            ->line('cash', cr: 450.00)
            ->doc("ck-no-1337-scan.pdf")
            ->post();

        $this->thisYear('2/12')
            ->transact('technical consulting services')
            ->line('accounts-receivable', dr: 240.00)
            ->line('services-revenue', cr: 240.00)
            ->doc("invoice-100.pdf")
            ->post();

        $this->thisYear('2/16')
            ->transact('ck no. 1338 - pay computers-ᴙ-us invoice')
            ->line('accounts-payable', dr: 5000.00)
            ->line('cash', cr: 5000.00)
            ->doc('ck-no-1338-scan.pdf')
            ->doc('computers-ᴙ-us-invoice-no-42.pdf')
            ->draft();

        $this->thisYear('2/26')
            ->transact('design services')
            ->line('accounts-receivable', dr: 480.00)
            ->line('services-revenue', cr: 480.00)
            ->doc("invoice-101.pdf")
            ->draft();
    }
}
