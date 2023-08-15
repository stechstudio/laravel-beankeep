<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;

final class AccountTest extends TestCase
{
    // -- ::debitPositive() ---------------------------------------------------

    public function testThatAssetAccountIsConsideredDebitPositive(): void
    {
        $this->assertTrue($this->assetAccount()->debitPositive());
    }

    public function testThatLiabilityAccountIsNotConsideredDebitPositive(): void
    {
        $this->assertFalse($this->liabilityAccount()->debitPositive());
    }

    public function testThatEquityAccountIsNotConsideredDebitPositive(): void
    {
        $this->assertFalse($this->equityAccount()->debitPositive());
    }

    public function testThatRevenueAccountIsNotConsideredDebitPositive(): void
    {
        $this->assertFalse($this->revenueAccount()->debitPositive());
    }

    public function testThatExpenseAccountIsConsideredDebitPositive(): void
    {
        $this->assertTrue($this->expenseAccount()->debitPositive());
    }

    // -- ::creditPositive() --------------------------------------------------

    // TODO(zmd): public function testThatAssetAccountIsNotConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatLiabilityAccountIsConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatEquityAccountIsConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatRevenueAccountIsConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatExpenseAccountIsNotConsideredCreditPositive(): void {}

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
