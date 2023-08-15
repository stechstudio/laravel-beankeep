<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Support\Ledger;

final class LedgerTest extends TestCase
{
    // -- ::balance() ---------------------------------------------------------

    // TODO(zmd): test ::balance()

    public function testTruth(): void
    {
        $this->assertTrue(false);
    }

    // -- ::computeBalance() --------------------------------------------------

    // TODO(zmd): test ::computeBalance()

    // -- ::debitPositiveBalance() --------------------------------------------

    // TODO(zmd): test ::debitPositiveBalance()

    // -- ::creditPositiveBalance() -------------------------------------------

    // TODO(zmd): test ::creditPositiveBalance()

    // ========================================================================

    private function assetAccount(): Account
    {
        return new Account([
            'number' => '1000',
            'name' => 'Assets',
            'type' => AccountType::Asset,
        ]);
    }

    private function liabilityAccount(): Account
    {
        return new Account([
            'number' => '2000',
            'name' => 'Liabilities',
            'type' => AccountType::Liability,
        ]);
    }

    private function equityAccount(): Account
    {
        return new Account([
            'number' => '3000',
            'name' => 'Equity',
            'type' => AccountType::Equity,
        ]);
    }

    private function revenueAccount(): Account
    {
        return new Account([
            'number' => '4000',
            'name' => 'Revenue',
            'type' => AccountType::Revenue,
        ]);
    }

    private function expenseAccount(): Account
    {
        return new Account([
            'number' => '5000',
            'name' => 'Expense',
            'type' => AccountType::Expense,
        ]);
    }
}
