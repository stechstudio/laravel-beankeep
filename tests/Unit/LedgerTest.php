<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Support\Ledger;
use STS\Beankeep\Support\LineItemCollection;
use STS\Beankeep\Tests\TestSupport\Traits\CanMakeLineItems;

final class LedgerTest extends TestCase
{
    use CanMakeLineItems;

    // -- ::balance() ---------------------------------------------------------

    public function testBalanceWithDebitPositiveAccountAndZeroStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->debitPositiveAccount(),
            startingBalance: 0,
            ledgerEntries: new LineItemCollection([
                $this->debit(100.00),  //    0.00 + 100.00 = 100.00
                $this->debit(50.00),   //  100.00 +  50.00 = 150.00
                $this->credit(10.00),  //  150.00 -  10.00 = 140.00
                $this->credit(50.00),  //  140.00 -  50.00 =  90.00
                $this->debit(10.00),   //   90.00 +  10.00 = 100.00
            ]),
        );

        $this->assertEquals(10000, $ledger->balance());
    }

    public function testBalanceWithDebitPositiveAccountAndPositiveStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->debitPositiveAccount(),
            startingBalance: 10000,
            ledgerEntries: new LineItemCollection([
                $this->debit(100.00),  //  100.00 + 100.00 = 200.00
                $this->debit(50.00),   //  200.00 +  50.00 = 250.00
                $this->credit(10.00),  //  250.00 -  10.00 = 240.00
                $this->credit(50.00),  //  240.00 -  50.00 = 190.00
                $this->debit(10.00),   //  190.00 +  10.00 = 200.00
            ]),
        );

        $this->assertEquals(20000, $ledger->balance());
    }

    public function testBalanceWithDebitPositiveAccountAndNegativeStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->debitPositiveAccount(),
            startingBalance: -5000,
            ledgerEntries: new LineItemCollection([
                $this->debit(100.00),  //  -50.00 + 100.00 =  50.00
                $this->debit(50.00),   //   50.00 +  50.00 = 100.00
                $this->credit(10.00),  //  100.00 -  10.00 =  90.00
                $this->credit(50.00),  //   90.00 -  50.00 =  40.00
                $this->debit(10.00),   //   40.00 +  10.00 =  50.00
            ]),
        );

        $this->assertEquals(5000, $ledger->balance());
    }

    public function testBalanceWithDebitPositiveAccountAndEntriesLeadingToNegativeBalance(): void
    {
        $ledger = new Ledger(
            account: $this->debitPositiveAccount(),
            startingBalance: -10000,
            ledgerEntries: new LineItemCollection([
                $this->debit(100.00),  // -100.00 + 100.00 =   0.00
                $this->debit(50.00),   //    0.00 +  50.00 =  50.00
                $this->credit(10.00),  //   50.00 -  10.00 =  40.00
                $this->credit(50.00),  //   40.00 -  50.00 = -10.00
                $this->debit(10.00),   //  -10.00 +  10.00 =   0.00
                $this->credit(30.00),  //    0.00 -  30.00 = -30.00
            ]),
        );

        $this->assertEquals(-3000, $ledger->balance());
    }

    public function testBalanceWithDebitPositiveAccountWithoutEntries(): void
    {
        $ledger = new Ledger(
            account: $this->debitPositiveAccount(),
            startingBalance: 10000,
            ledgerEntries: new LineItemCollection(),
        );

        $this->assertEquals(10000, $ledger->balance());
    }

    public function testBalanceWithCreditPositiveAccountAndZeroStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->creditPositiveAccount(),
            startingBalance: 0,
            ledgerEntries: new LineItemCollection([
                $this->credit(100.00),  //    0.00 + 100.00 = 100.00
                $this->credit(50.00),   //  100.00 +  50.00 = 150.00
                $this->debit(10.00),    //  150.00 -  10.00 = 140.00
                $this->debit(50.00),    //  140.00 -  50.00 =  90.00
                $this->credit(10.00),   //   90.00 +  10.00 = 100.00
            ]),
        );

        $this->assertEquals(10000, $ledger->balance());
    }

    public function testBalanceWithCreditPositiveAccountAndPositiveStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->creditPositiveAccount(),
            startingBalance: 10000,
            ledgerEntries: new LineItemCollection([
                $this->credit(100.00),  //  100.00 + 100.00 = 200.00
                $this->credit(50.00),   //  200.00 +  50.00 = 250.00
                $this->debit(10.00),    //  250.00 -  10.00 = 240.00
                $this->debit(50.00),    //  240.00 -  50.00 = 190.00
                $this->credit(10.00),   //  190.00 +  10.00 = 200.00
            ]),
        );

        $this->assertEquals(20000, $ledger->balance());
    }

    public function testBalanceWithCreditPositiveAccountAndNegativeStartingBalance(): void
    {
        $ledger = new Ledger(
            account: $this->creditPositiveAccount(),
            startingBalance: -5000,
            ledgerEntries: new LineItemCollection([
                $this->credit(100.00),  //  -50.00 + 100.00 =  50.00
                $this->credit(50.00),   //   50.00 +  50.00 = 100.00
                $this->debit(10.00),    //  100.00 -  10.00 =  90.00
                $this->debit(50.00),    //   90.00 -  50.00 =  40.00
                $this->credit(10.00),   //   40.00 +  10.00 =  50.00
            ]),
        );

        $this->assertEquals(5000, $ledger->balance());
    }

    public function testBalanceWithCreditPositiveAccountAndEntriesLeadingToNegativeBalance(): void
    {
        $ledger = new Ledger(
            account: $this->creditPositiveAccount(),
            startingBalance: -10000,
            ledgerEntries: new LineItemCollection([
                $this->credit(100.00),  // -100.00 + 100.00 =   0.00
                $this->credit(50.00),   //    0.00 +  50.00 =  50.00
                $this->debit(10.00),    //   50.00 -  10.00 =  40.00
                $this->debit(50.00),    //   40.00 -  50.00 = -10.00
                $this->credit(10.00),   //  -10.00 +  10.00 =   0.00
                $this->debit(30.00),    //    0.00 -  30.00 = -30.00
            ]),
        );

        $this->assertEquals(-3000, $ledger->balance());
    }

    public function testBalanceWithCreditPositiveAccountWithoutEntries(): void
    {
        $ledger = new Ledger(
            account: $this->creditPositiveAccount(),
            startingBalance: 10000,
            ledgerEntries: new LineItemCollection(),
        );

        $this->assertEquals(10000, $ledger->balance());
    }

    // -- ::computeBalance() --------------------------------------------------

    public function testComputeBalanceWithDebitPositiveAccountAndZeroStartingBalance(): void
    {
        $this->assertEquals(5000, Ledger::computeBalance(
            account: $this->debitPositiveAccount(),
            startingBalance: 0,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testComputeBalanceWithDebitPositiveAccountAndPositiveStartingBalance(): void
    {
        $this->assertEquals(15000, Ledger::computeBalance(
            account: $this->debitPositiveAccount(),
            startingBalance: 10000,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testComputeBalanceWithDebitPositiveAccountAndNegativeStartingBalance(): void
    {
        $this->assertEquals(0, Ledger::computeBalance(
            account: $this->debitPositiveAccount(),
            startingBalance: -5000,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testComputeBalanceWithDebitPositiveAccountWithDebitAndCreditSumsLeadingToNegativeBalance(): void
    {
        $this->assertEquals(-5000, Ledger::computeBalance(
            account: $this->debitPositiveAccount(),
            startingBalance: 0,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testComputeBalanceWithDebitPositiveAccountAndZeroDebitAndCreditSums(): void
    {
        $this->assertEquals(10000, Ledger::computeBalance(
            account: $this->debitPositiveAccount(),
            startingBalance: 10000,
            debitSum: 0,
            creditSum: 0,
        ));
    }

    public function testComputeBalanceWithCreditPositiveAccountAndZeroStartingBalance(): void
    {
        $this->assertEquals(5000, Ledger::computeBalance(
            account: $this->creditPositiveAccount(),
            startingBalance: 0,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testComputeBalanceWithCreditPositiveAccountAndPositiveStartingBalance(): void
    {
        $this->assertEquals(15000, Ledger::computeBalance(
            account: $this->creditPositiveAccount(),
            startingBalance: 10000,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testComputeBalanceWithCreditPositiveAccountAndNegativeStartingBalance(): void
    {
        $this->assertEquals(0, Ledger::computeBalance(
            account: $this->creditPositiveAccount(),
            startingBalance: -5000,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testComputeBalanceWithCreditPositiveAccountWithDebitAndCreditSumsLeadingToNegativeBalance(): void
    {
        $this->assertEquals(-5000, Ledger::computeBalance(
            account: $this->creditPositiveAccount(),
            startingBalance: 0,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testComputeBalanceWithCreditPositiveAccountAndZeroDebitAndCreditSums(): void
    {
        $this->assertEquals(10000, Ledger::computeBalance(
            account: $this->creditPositiveAccount(),
            startingBalance: 10000,
            debitSum: 0,
            creditSum: 0,
        ));
    }

    // -- ::debitPositiveBalance() --------------------------------------------

    public function testDebitPositiveBalanceWithZeroStartingBalance(): void
    {
        $this->assertEquals(5000, Ledger::debitPositiveBalance(
            startingBalance: 0,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testDebitPositiveBalanceWithPositiveStartingBalance(): void
    {
        $this->assertEquals(15000, Ledger::debitPositiveBalance(
            startingBalance: 10000,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testDebitPositiveBalanceWithNegativeStartingBalance(): void
    {
        $this->assertEquals(0, Ledger::debitPositiveBalance(
            startingBalance: -5000,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testDebitPositiveBalanceWithDebitAndCreditSumsLeadingToNegativeBalance(): void
    {
        $this->assertEquals(-5000, Ledger::debitPositiveBalance(
            startingBalance: 0,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testDebitPositiveBalanceWithZeroDebitAndCreditSums(): void
    {
        $this->assertEquals(10000, Ledger::debitPositiveBalance(
            startingBalance: 10000,
            debitSum: 0,
            creditSum: 0,
        ));
    }

    // -- ::creditPositiveBalance() -------------------------------------------

    public function testCreditPositiveBalanceWithZeroStartingBalance(): void
    {
        $this->assertEquals(5000, Ledger::creditPositiveBalance(
            startingBalance: 0,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testCreditPositiveBalanceWithPositiveStartingBalance(): void
    {
        $this->assertEquals(15000, Ledger::creditPositiveBalance(
            startingBalance: 10000,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testCreditPositiveBalanceWithNegativeStartingBalance(): void
    {
        $this->assertEquals(0, Ledger::creditPositiveBalance(
            startingBalance: -5000,
            debitSum: 5000,
            creditSum: 10000,
        ));
    }

    public function testCreditPositiveBalanceWithDebitAndCreditSumsLeadingToNegativeBalance(): void
    {
        $this->assertEquals(-5000, Ledger::creditPositiveBalance(
            startingBalance: 0,
            debitSum: 10000,
            creditSum: 5000,
        ));
    }

    public function testCreditPositiveBalanceWithZeroDebitAndCreditSums(): void
    {
        $this->assertEquals(10000, Ledger::creditPositiveBalance(
            startingBalance: 10000,
            debitSum: 0,
            creditSum: 0,
        ));
    }

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
}
