<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class LedgerEntriesTest extends TestCase
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
            $txn(  '2/12/2023',  dr: ['accounts-receivable',   240.00], cr: ['services-revenue',      240.00]);
            $draft('2/16/2023',  dr: ['accounts-payable',     5000.00], cr: ['cash',                 5000.00]);
            $draft('2/26/2023',  dr: ['accounts-receivable',   480.00], cr: ['services-revenue',      480.00]);
        });
    }

    public function testItCanGetLedgerEntriesForSpecificPeriod(): void
    {
        $account = $this->accounts['cash'];
        $janLedgerItems = $this->accounts['cash']->lineItems()->ledgerEntries($this->janPeriod())->get();
        $febLedgerItems = $this->accounts['cash']->lineItems()->ledgerEntries($this->febPeriod())->get();

        $this->assertEquals(1, $janLedgerItems->count());
        $this->assertEquals(1500, $janLedgerItems[0]->credit);
        $this->assertTrue($janLedgerItems[0]->transaction->posted);
        $this->assertEquals(CarbonImmutable::parse('1/10/2023'), $janLedgerItems[0]->transaction->date);

        $this->assertEquals(1, $febLedgerItems->count());
        $this->assertEquals(45000, $febLedgerItems[0]->credit);
        $this->assertTrue($febLedgerItems[0]->transaction->posted);
        $this->assertEquals(CarbonImmutable::parse('2/1/2023'), $febLedgerItems[0]->transaction->date);
    }

    public function testItCanGetLedgerEntriesPriorToSpecificPeriod(): void
    {
        $account = $this->accounts['cash'];
        $priorToJanLedgerItems = $this->accounts['cash']->lineItems()->ledgerEntries(priorTo: $this->janPeriod())->get();

        $this->assertEquals(1, $priorToJanLedgerItems->count());
        $this->assertEquals(1000000, $priorToJanLedgerItems[0]->debit);
        $this->assertTrue($priorToJanLedgerItems[0]->transaction->posted);
        $this->assertEquals(CarbonImmutable::parse('12/25/2022'), $priorToJanLedgerItems[0]->transaction->date);
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
