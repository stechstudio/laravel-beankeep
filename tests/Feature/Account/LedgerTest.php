<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class LedgerTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
    }

    public function testItCanConstructLedgerObjectForDebitPositiveAccountAndPeriod(): void
    {
        $janLedger = $this->account('cash')->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountAndPeriod(): void
    {
        $janLedger = $this->account('accounts-payable')->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForDebitPositiveAccountCorrectlyExcludingUnpostedTransactionsInThePast(): void
    {
        $this->lastYear('12/27')
            ->transact('buy office supplies')
            ->line('supplies-expense', dr: 50.00)
            ->line('cash', cr: 50.00)
            ->doc('office-smacks-receipt.pdf')
            ->draft();

        $janLedger = $this->account('cash')->ledger($this->janPeriod());

        $this->assertEquals(998500, $janLedger->balance());
    }

    public function testItCanConstructLedgerObjectForCreditPositiveAccountCorrectlyExcludingUnpostedTransactionsInThePast(): void
    {
        $this->lastYear('12/28')
            ->transact('1 optical mouse from computers-ᴙ-us')
            ->line('equipment', dr: 25.00)
            ->line('accounts-payable', cr: 25.00)
            ->doc('computers-ᴙ-us-receipt.pdf')
            ->draft();

        $janLedger = $this->account('accounts-payable')->ledger($this->janPeriod());

        $this->assertEquals(500000, $janLedger->balance());
    }
}
