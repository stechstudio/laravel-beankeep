<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class AccountLedgerTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
    }

    // NOTE(zmd): right now the default period is the current calendar year; we
    //   will need to update this test once we make the default period
    //   user-configurable
    public function testItCanGetLedgerTransactionsForDefaultPeriod(): void
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

    public function testItCanGetLedgerTransactionsForSpecificPeriod(): void
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

    public function testItCanConstructLedgerObjectForDebitPositiveAccountAndPeriod(): void
    {
        $janLedger = $this->account('cash')->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanReportDebitPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(
            998500,
            $this->account('cash')->balance($this->janPeriod()),
        );
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountAndPeriod(): void
    {
        $janLedger = $this->account('accounts-payable')->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }

    public function testItCanReportCreditPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(
            500000,
            $this->account('accounts-payable')->balance($this->janPeriod()),
        );
    }
}
