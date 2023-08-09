<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;
use STS\Beankeep\Enums\AccountType;

final class AccountTypeTest extends TestCase
{
    // -- ::debitPositive() --------------------------------------------------

    public function testDebitPositiveReturnsTrueForAssetType(): void
    {
        $this->assertTrue(AccountType::Asset->debitPositive());
    }

    public function testDebitPositiveReturnsFalseForLiabilityType(): void
    {
        $this->assertFalse(AccountType::Liability->debitPositive());
    }

    public function testDebitPositiveReturnsFalseForEquityType(): void
    {
        $this->assertFalse(AccountType::Equity->debitPositive());
    }

    public function testDebitPositiveReturnsFalseForRevenueType(): void
    {
        $this->assertFalse(AccountType::Revenue->debitPositive());
    }

    public function testDebitPositiveReturnsTrueForExpenseType(): void
    {
        $this->assertTrue(AccountType::Expense->debitPositive());
    }

    // -- ::creditPositive() --------------------------------------------------

    public function testCreditPositiveReturnsFalseForAssetType(): void
    {
        $this->assertFalse(AccountType::Asset->creditPositive());
    }

    public function testCreditPositiveReturnsTrueForLiabilityType(): void
    {
        $this->assertTrue(AccountType::Liability->creditPositive());
    }

    public function testCreditPositiveReturnsTrueForEquityType(): void
    {
        $this->assertTrue(AccountType::Equity->creditPositive());
    }

    public function testCreditPositiveReturnsTrueForRevenueType(): void
    {
        $this->assertTrue(AccountType::Revenue->creditPositive());
    }

    public function testCreditPositiveReturnsFalseForExpenseType(): void
    {
        $this->assertFalse(AccountType::Expense->creditPositive());
    }
}
