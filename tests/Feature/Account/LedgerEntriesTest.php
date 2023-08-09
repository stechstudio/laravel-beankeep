<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class LedgerEntriesTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
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
}
