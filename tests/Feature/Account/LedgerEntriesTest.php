<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasTransactionMakingShortcuts;

final class LedgerEntriesTest extends TestCase
{
    use HasTransactionMakingShortcuts;

    public function setUp(): void
    {
        parent::setUp();

        $this->createAccountsIfMissing();

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

    public function testItCanGetLedgerEntriesForDefaultPeriod(): void
    {
        $account = $this->account('cash');
        $ledgerEntries = $account->lineItems()->ledgerEntries()->get();

        $this->assertEquals(2, $ledgerEntries->count());

        $this->assertEquals(1500, $ledgerEntries[0]->credit);
        $this->assertTrue($ledgerEntries[0]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '1/10'), $ledgerEntries[0]->transaction->date);

        $this->assertEquals(45000, $ledgerEntries[1]->credit);
        $this->assertTrue($ledgerEntries[1]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '2/1'), $ledgerEntries[1]->transaction->date);
    }

    public function testItCanGetLedgerEntriesForSpecificPeriod(): void
    {
        $account = $this->account('cash');
        $janLedgerItems = $this->account('cash')->lineItems()->ledgerEntries($this->janPeriod())->get();
        $febLedgerItems = $this->account('cash')->lineItems()->ledgerEntries($this->febPeriod())->get();

        $this->assertEquals(1, $janLedgerItems->count());
        $this->assertEquals(1500, $janLedgerItems[0]->credit);
        $this->assertTrue($janLedgerItems[0]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '1/10'), $janLedgerItems[0]->transaction->date);

        $this->assertEquals(1, $febLedgerItems->count());
        $this->assertEquals(45000, $febLedgerItems[0]->credit);
        $this->assertTrue($febLedgerItems[0]->transaction->posted);
        $this->assertEquals($this->getDate(thisYear: '2/1'), $febLedgerItems[0]->transaction->date);
    }

    public function testItCanGetLedgerEntriesPriorToSpecificPeriod(): void
    {
        $account = $this->account('cash');
        $priorToJanLedgerItems = $this->account('cash')->lineItems()->ledgerEntries(priorTo: $this->janPeriod())->get();

        $this->assertEquals(1, $priorToJanLedgerItems->count());
        $this->assertEquals(1000000, $priorToJanLedgerItems[0]->debit);
        $this->assertTrue($priorToJanLedgerItems[0]->transaction->posted);
        $this->assertEquals($this->getDate(lastYear: '12/25'), $priorToJanLedgerItems[0]->transaction->date);
    }
}
