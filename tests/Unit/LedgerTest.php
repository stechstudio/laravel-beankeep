<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Support\Ledger;
use STS\Beankeep\Support\LineItemCollection;

final class LedgerTest extends TestCase
{
    // -- ::balance() ---------------------------------------------------------

    public function testBalanceWithDebitPositiveAccountAndZeroStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->debitPositiveAccount(),
            startingBalance: 0,
            ledgerEntries: new LineItemCollection([
                $this->debit(100.00),  //   0.00 + 100.00 = 100.00
                $this->debit(50.00),   // 100.00 +  50.00 = 150.00
                $this->credit(10.00),  // 150.00 -  10.00 = 140.00
                $this->credit(50.00),  // 140.00 -  50.00 =  90.00
                $this->debit(10.00),   //  90.00 +  10.00 = 100.00
            ]),
        );

        $this->assertEquals(10000, $ledger->balance());
    }

    // TODO(zmd): public function testBalanceWithDebitPositiveAccountAndPositiveStartingBalance(): void {}

    // TODO(zmd): public function testBalanceWithDebitPositiveAccountAndNegativeStartingBalance(): void {}

    // TODO(zmd): public function testBalanceWithDebitPositiveAccountAndEntriesLeadingToNegativeBalance(): void {}

    // TODO(zmd): public function testBalanceWithDebitPositiveAccountWihtoutEntries(): void {}


    // TODO(zmd): public function testBalanceWithCreditPositiveAccountAndZeroStartingBalance(): void {}

    // TODO(zmd): public function testBalanceWithCreditPositiveAccountAndPositiveStartingBalance(): void {}

    // TODO(zmd): public function testBalanceWithCreditPositiveAccountAndNegativeStartingBalance(): void {}

    // TODO(zmd): public function testBalanceWithCreditPositiveAccountAndEntriesLeadingToNegativeBalance(): void {}

    // TODO(zmd): public function testBalanceWithCreditPositiveAccountWihtoutEntries(): void {}

    // -- ::computeBalance() --------------------------------------------------

    // TODO(zmd): test ::computeBalance()

    // -- ::debitPositiveBalance() --------------------------------------------

    // TODO(zmd): test ::debitPositiveBalance()

    // -- ::creditPositiveBalance() -------------------------------------------

    // TODO(zmd): test ::creditPositiveBalance()

    // ========================================================================

    private function debitPositiveAccount(): Account
    {
        return new Account([
            'number' => '1000',
            'name' => 'Assets',
            'type' => AccountType::Asset,
        ]);
    }

    private function creditPositiveAccount(): Account
    {
        return new Account([
            'number' => '2000',
            'name' => 'Liabilities',
            'type' => AccountType::Liability,
        ]);
    }

    private function debit(float $amount): LineItem
    {
        return new LineItem(['debit' => $this->floatToInt($amount)]);
    }

    private function credit(float $amount): LineItem
    {
        return new LineItem(['credit' => $this->floatToInt($amount)]);
    }

    private function floatToInt(float $amount): int
    {
        return (int) ($amount * 100);
    }
}
