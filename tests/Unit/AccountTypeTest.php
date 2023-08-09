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

    public function testDebitPositiveReturnsFalseForExpenseType(): void
    {
        $this->assertTrue(AccountType::Expense->debitPositive());
    }

    // -- ::creditPositive() --------------------------------------------------

    // TODO(zmd): test credit-positive w/asset type -> false

    // TODO(zmd): test credit-positive w/liability type -> true

    // TODO(zmd): test credit-positive w/equity type -> true

    // TODO(zmd): test credit-positive w/revenue type -> true

    // TODO(zmd): test credit-positive w/expense type -> false
}
