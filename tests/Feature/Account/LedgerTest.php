<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class LedgerTest extends TestCase
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

    public function testItCanConstructLedgerObjectForDebitPositiveAccountForSpecifiedPeriod(): void
    {
        $janLedger = $this->accounts['cash']->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForDebitPositiveAccountForJournalsCurrentPeriod(): void
    {
        $ledger = $this->accounts['cash']->ledger();

        $this->assertEquals(953500, $ledger->balance());
    }

    public function testItCanConstructLedgerObjectForDebitPositiveAccountCorrectlyExcludingUnpostedTransactionsInThePast(): void
    {
        $this->draft('12/27/2022', dr: ['supplies-expense', 50.00], cr: ['cash', 50.00]);

        $janLedger = $this->accounts['cash']->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountAndSpecifiedPeriod(): void
    {
        $janLedger = $this->accounts['accounts-payable']->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountForJournalsCurrentPeriod(): void
    {
        $ledger = $this->accounts['accounts-payable']->ledger();

        $this->assertEquals(750000, $ledger->balance());
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountCorrectlyExcludingUnpostedTransactionsInThePast(): void
    {
        $this->draft('12/28/2022', dr: ['equipment', 25.00], cr: ['accounts-payable', 25.00]);

        $janLedger = $this->accounts['accounts-payable']->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }

    // ------------------------------------------------------------------------

    protected function janPeriod(): CarbonPeriod
    {
        $start = CarbonImmutable::parse('1/1/2023');
        $end = CarbonImmutable::parse('1/31/2023');

        return $start->daysUntil($end);
    }
}
