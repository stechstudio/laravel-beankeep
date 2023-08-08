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
}
