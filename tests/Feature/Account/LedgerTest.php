<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasTransactionMakingShortcuts;

final class LedgerTest extends TestCase
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
        $this->txn(     thisYear: '2/5',   dr: ['equipment',            2500.00], cr: ['accounts-payable',     2500.00]);
        $this->txn(     thisYear: '2/12',  dr: ['accounts-receivable',   240.00], cr: ['services-revenue',      240.00]);
        $this->draftTxn(thisYear: '2/16',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
        $this->draftTxn(thisYear: '2/26',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
    }

    public function testItCanConstructLedgerObjectForDebitPositiveAccountForSpecifiedPeriod(): void
    {
        $janLedger = $this->account('cash')->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForDebitPositiveAccountForDefaultPeriod(): void
    {
        $ledger = $this->account('cash')->ledger();

        $this->assertEquals(953500, $ledger->balance());
    }

    // TODO(zmd): public function testItCanConstructLedgerObjectForDebitPositiveAccountForConfiguredDefaultPeriod(): void {}

    public function testItCanConstructLedgerObjectForCreditPositiveAccountAndSpecifiedPeriod(): void
    {
        $janLedger = $this->account('accounts-payable')->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }

    // TODO(zmd): public function testItCanConstructLedgerObjectForCreditPositiveAccountForDefaultPeriod(): void {}

    // TODO(zmd): public function testItCanConstructLedgerObjectForCreditPositiveAccountForConfiguredDefaultPeriod(): void {}

    public function testItCanConstructLedgerObjectForDebitPositiveAccountCorrectlyExcludingUnpostedTransactionsInThePast(): void
    {
        $this->draftTxn(lastYear: '12/27', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $janLedger = $this->account('cash')->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountCorrectlyExcludingUnpostedTransactionsInThePast(): void
    {
        $this->draftTxn(lastYear: '12/28', dr: ['equipment', 25.00], cr: ['accounts-payable', 25.00]);

        $janLedger = $this->account('accounts-payable')->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }
}
