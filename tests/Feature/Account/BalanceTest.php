<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasTransactionMakingShortcuts;

final class BalanceTest extends TestCase
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
        $this->draftTxn(lastYear: '2/16',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
        $this->draftTxn(lastYear: '2/26',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
    }

    // -- ::balance() ---------------------------------------------------------

    public function testItCanReportDebitPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(998500, $this->account('cash')->balance($this->janPeriod()));
    }

    public function testItCanReportDebitPositiveBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(953500, $this->account('cash')->balance());
    }

    public function testItCanReportDebitPositiveBalanceForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->getDate(thisYear: '3/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(953500, $this->account('cash')->balance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveBalanceCalculations(): void
    {
        $this->draftTxn(lastYear: '12/27', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(998500, $this->account('cash')->balance($this->janPeriod()));
    }

    public function testItCanReportCreditPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(500000, $this->account('accounts-payable')->balance($this->janPeriod()));
    }

    public function testItCanReportCreditPositiveBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(750000, $this->account('accounts-payable')->balance());
    }

    public function testItCanReportCreditPositiveBalanceForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->getDate(thisYear: '3/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(750000, $this->account('accounts-payable')->balance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveBalanceCalculations(): void
    {
        $this->draftTxn(lastYear: '12/28', dr: ['equipment', 25.00], cr: ['accounts-payable', 25.00]);

        $this->assertEquals(500000, $this->account('accounts-payable')->balance($this->janPeriod()));
    }

    // -- ::openingBalance() --------------------------------------------------

    public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForGivenPeriod(): void
    {
        $this->assertEquals(998500, $this->account('cash')->openingBalance($this->febPeriod()));
    }

    public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(1000000, $this->account('cash')->openingBalance());
    }

    public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->getDate(thisYear: '3/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(0, $this->account('cash')->openingBalance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveOpeningBalanceCalculations(): void
    {
        $this->draftTxn(lastYear: '12/27', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(998500, $this->account('cash')->openingBalance($this->febPeriod()));
    }

    public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForGivenPeriod(): void
    {
        $this->assertEquals(500000, $this->account('accounts-payable')->openingBalance($this->febPeriod()));
    }

    public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(0, $this->account('accounts-payable')->openingBalance());
    }

    public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->getDate(thisYear: '3/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(0, $this->account('accounts-payable')->openingBalance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveOpeningBalanceCalculations(): void
    {
        $this->draftTxn(lastYear: '12/28', dr: ['equipment', 25.00], cr: ['accounts-payable', 25.00]);

        $this->assertEquals(750000, $this->account('accounts-payable')->balance($this->febPeriod()));
    }
}
