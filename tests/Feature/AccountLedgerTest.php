<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Carbon\CarbonPeriod;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\CanCreateAccounts;

final class AccountLedgerTest extends TestCase
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    public function setUp(): void
    {
        parent::setUp();
        $this->createAccounts();
    }

    // NOTE(zmd): right now the default period is the current calendar year; we
    //   will need to update this test once we make the default period
    //   user-configurable
    public function testItCanGetLedgerTransactionsForDefaultPeriod(): void
    {
        $this->threeMonthsOfTransactions();

        $account = $this->account('cash');
        $ledger = $account->ledger;

        $this->assertEquals(2, $ledger->count());

        $this->assertEquals(1500, $ledger[0]->credit);
        $this->assertTrue($ledger[0]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '1/10'), $ledger[0]->transaction->date);

        $this->assertEquals(45000, $ledger[1]->credit);
        $this->assertTrue($ledger[1]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '2/1'), $ledger[1]->transaction->date);
    }

    public function testItCanGetLedgerTransactionsForSpecificPeriod(): void
    {
        $this->threeMonthsOfTransactions();

        $account = $this->account('cash');
        $janLedger = $this->account('cash')->ledger($this->janPeriod())->get();
        $febLedger = $this->account('cash')->ledger($this->febPeriod())->get();

        $this->assertEquals(1, $janLedger->count());
        $this->assertEquals(1500, $janLedger[0]->credit);
        $this->assertTrue($janLedger[0]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '1/10'), $janLedger[0]->transaction->date);

        $this->assertEquals(1, $febLedger->count());
        $this->assertEquals(45000, $febLedger[0]->credit);
        $this->assertTrue($febLedger[0]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '2/1'), $febLedger[0]->transaction->date);
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

    protected function threeMonthsOfTransactions(): void
    {
        $this->lastYear('12/25')
            ->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->doc('contribution-moa.pdf')
            ->post();

        $this->thisYear('1/5')
            ->transact('develpment services')
            ->line('accounts-receivable', dr: 1200.00)
            ->line('services-revenue', cr: 1200.00)
            ->doc("invoice-99.pdf")
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
