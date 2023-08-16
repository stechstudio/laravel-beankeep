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

        $this->txn(     lastYear: '12/25', dr: ['cash',                10000.00], cr: ['capital',             10000.00]);
        $this->txn(     thisYear: '1/5',   dr: ['accounts-receivable',  1200.00], cr: ['services-revenue',     1200.00]);
        $this->txn(     thisYear: '1/10',  dr: ['cost-of-services',       15.00], cr: ['cash',                   15.00]);
        $this->txn(     thisYear: '1/20',  dr: ['equipment',            5000.00], cr: ['accounts-payable',     5000.00]);
        $this->txn(     thisYear: '2/1',   dr: ['rent-expense',          450.00], cr: ['cash',                  450.00]);
        $this->txn(     thisYear: '2/12',  dr: ['accounts-receivable',   240.00], cr: ['services-revenue',      240.00]);
        $this->drafttxn(thisYear: '2/16',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
        $this->draftTxn(thisYear: '2/26',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
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
