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
        $assetAccount = new Account([
            'number' => '1000',
            'name' => 'Assets',
            'type' => AccountType::Asset,
        ]);

        $this->assertTrue($assetAccount->debitPositive());
    }

    public function testThatLiabilityAccountIsNotConsideredDebitPositive(): void
    {
        $assetAccount = new Account([
            'number' => '2000',
            'name' => 'Liabilities',
            'type' => AccountType::Liability,
        ]);

        $this->assertFalse($assetAccount->debitPositive());
    }

    // TODO(zmd): public function testThatEquityAccountIsNotConsideredDebitPositive(): void {}

    // TODO(zmd): public function testThatRevenueAccountIsNotConsideredDebitPositive(): void {}

    // TODO(zmd): public function testThatExpenseAccountIsConsideredDebitPositive(): void {}

    // -- ::creditPositive() --------------------------------------------------

    // TODO(zmd): public function testThatAssetAccountIsNotConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatLiabilityAccountIsConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatEquityAccountIsConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatRevenueAccountIsConsideredCreditPositive(): void {}

    // TODO(zmd): public function testThatExpenseAccountIsNotConsideredCreditPositive(): void {}
}
