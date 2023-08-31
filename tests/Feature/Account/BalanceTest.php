<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class BalanceTest extends TestCase
{
    use GeneratesJournalData;

    protected array $accounts;

    public function setUp(): void
    {
        parent::setUp();

        [$_journal, $this->accounts] = $this->for('jan', function ($txn, $draft) {
            $txn(  '12/25/2022', dr: ['cash',                10000.00], cr: ['capital',             10000.00]);
            $txn(  '1/5/2023',   dr: ['accounts-receivable',  1200.00], cr: ['services-revenue',     1200.00]);
            $txn(  '1/10/2023',  dr: ['cost-of-services',       15.00], cr: ['cash',                   15.00]);
            $txn(  '1/20/2023',  dr: ['equipment',            5000.00], cr: ['accounts-payable',     5000.00]);
            $txn(  '2/1/2023',   dr: ['rent-expense',          450.00], cr: ['cash',                  450.00]);
            $txn(  '2/5/2023',   dr: ['equipment',            2500.00], cr: ['accounts-payable',     2500.00]);
            $txn(  '2/12/2023',  dr: ['accounts-receivable',   240.00], cr: ['services-revenue',      240.00]);
            $draft('2/16/2023',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
            $draft('2/26/2023',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
        });
    }

    // -- ::balance() ---------------------------------------------------------

    public function testItCanReportDebitPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(998500, $this->accounts['cash']->balance($this->janPeriod()));
    }

    public function testItCanReportDebitPositiveBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(953500, $this->accounts['cash']->balance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveBalanceCalculations(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(998500, $this->accounts['cash']->balance($this->janPeriod()));
    }

    public function testItCanReportCreditPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(500000, $this->accounts['accounts-payable']->balance($this->janPeriod()));
    }

    public function testItCanReportCreditPositiveBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(750000, $this->accounts['accounts-payable']->balance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveBalanceCalculations(): void
    {
        $this->draftTxn('12/28/2022', dr: ['equipment', 25.00], cr: ['accounts-payable', 25.00]);

        $this->assertEquals(500000, $this->accounts['accounts-payable']->balance($this->janPeriod()));
    }

    // -- ::openingBalance() --------------------------------------------------

    public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForGivenPeriod(): void
    {
        $this->assertEquals(998500, $this->accounts['cash']->openingBalance($this->febPeriod()));
    }

    public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(1000000, $this->accounts['cash']->openingBalance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveOpeningBalanceCalculations(): void
    {
        $this->draftTxn('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $this->assertEquals(998500, $this->accounts['cash']->openingBalance($this->febPeriod()));
    }

    public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForGivenPeriod(): void
    {
        $this->assertEquals(500000, $this->accounts['accounts-payable']->openingBalance($this->febPeriod()));
    }

    public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForDefaultPeriod(): void
    {
        $this->assertEquals(0, $this->accounts['accounts-payable']->openingBalance());
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveOpeningBalanceCalculations(): void
    {
        $this->draftTxn('12/28/2022', dr: ['equipment', 25.00], cr: ['accounts-payable', 25.00]);

        $this->assertEquals(750000, $this->accounts['accounts-payable']->balance($this->febPeriod()));
    }

    // ------------------------------------------------------------------------

    protected function janPeriod(): CarbonPeriod
    {
        $start = CarbonImmutable::parse('1/1/2023');
        $end = CarbonImmutable::parse('1/31/2023');

        return $start->daysUntil($end);
    }

    protected function febPeriod(): CarbonPeriod
    {
        $start = CarbonImmutable::parse('2/1/2023');
        $end = CarbonImmutable::parse('2/28/2023');

        return $start->daysUntil($end);
    }
}
