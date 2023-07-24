<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Tests\TestCase;

final class TransactionPostingTest extends TestCase
{

    // -- ::canPost() --------------------------------------------------

    public function testCanPostReturnsTrueWhenDebitsAndCreditsBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 40000));

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitDebits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'pay interest on loan (including accrued interest from prior year)',
        ]);

        $transaction->lineItems()->save($this->debit('interestPayable', 20000));
        $transaction->lineItems()->save($this->debit('interestExpense', 20000));
        $transaction->lineItems()->save($this->credit('cash', 40000));

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitCredits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'buy netbook (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 40000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitDebitsAndSplitCredits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-05-05'),
            'memo' => 'buy netbook with extended damage insurance (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 20000));
        $transaction->lineItems()->save($this->debit('prepaidInsurance', 20000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenDebitsAndCreditsDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 30000));

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndCreditDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'pay interest on loan (including accrued interest from prior year)',
        ]);

        $transaction->lineItems()->save($this->debit('interestPayable', 20000));
        $transaction->lineItems()->save($this->debit('interestExpense', 20000));
        $transaction->lineItems()->save($this->credit('cash', 40002));

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenDebitAndSplitCreditsDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'buy netbook (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 40000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 19999));

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndSplitCreditDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-05-05'),
            'memo' => 'buy netbook with extended damage insurance (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 20000));
        $transaction->lineItems()->save($this->debit('prepaidInsurance', 20000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20010));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutLineItems(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyDebits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->credit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 40000));

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyCredits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->debit('revenue', 40000));

        $this->assertFalse($transaction->canPost());
    }

    // -- post via ::save() ---------------------------------------------------

    public function testSaveAllowsPostingWhenLineItemsDebitsAndCreditsBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 40000));

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitDebits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'pay interest on loan (including accrued interest from prior year)',
        ]);

        $transaction->lineItems()->save($this->debit('interestPayable', 20000));
        $transaction->lineItems()->save($this->debit('interestExpense', 20000));
        $transaction->lineItems()->save($this->credit('cash', 40000));

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitCredits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'buy netbook (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 40000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitDebitsWithSplitCredits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-05-05'),
            'memo' => 'buy netbook with extended damage insurance (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 20000));
        $transaction->lineItems()->save($this->debit('prepaidInsurance', 20000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowedForUnbalancedLineItemsAsLongAsPostedRemainsFalse(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 30000));

        $transaction->memo = 'perform PREMIUM services';

        $this->assertTrue($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWhenLineItemsDebitsAndCreditsDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 30000));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWhenLineItemsSplitDebitsAndCreditDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'pay interest on loan (including accrued interest from prior year)',
        ]);

        $transaction->lineItems()->save($this->debit('interestPayable', 20000));
        $transaction->lineItems()->save($this->debit('interestExpense', 20000));
        $transaction->lineItems()->save($this->credit('cash', 40002));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWhenLineItemsDebitAndSplitCreditsDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'buy netbook (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 40000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 19999));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWhenLineItemsSplitDebitsAndSplitCreditDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-05-05'),
            'memo' => 'buy netbook with extended damage insurance (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 20000));
        $transaction->lineItems()->save($this->debit('prepaidInsurance', 20000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20010));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWithoutLineItems(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveNewDisallowsPostingBecauseNoLineItemsArePossiblyAssociatedYet(): void
    {
        $transaction = new Transaction([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->exists);
    }

    public function testSaveRequiresAtLeastOneDebit(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->credit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 40000));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveRequiresAtLeastOneCredit(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->debit('revenue', 40000));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    // ------------------------------------------------------------------------

    private array $memoizedAccounts = [];

    private function debit(Account|string $account, int $amount): LineItem
    {
        $debit = new LineItem(['debit' => $amount, 'credit' => 0]);
        $debit->account()->associate($this->lookupAccount($account));

        return $debit;
    }

    private function credit(Account|string $account, int $amount): LineItem
    {
        $credit = new LineItem(['debit' => 0, 'credit' => $amount]);
        $credit->account()->associate($this->lookupAccount($account));

        return $credit;
    }

    private function lookupAccount(Account|string $account): Account
    {
        if (is_string($account)) {
            return $this->$account();
        }

        return $account;
    }

    private function cash(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '1100',
            'type' => AccountType::Asset,
            'name' => 'Cash',
        ]);
    }

    private function accountsReceivable(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '1200',
            'type' => AccountType::Asset,
            'name' => 'Accounts Receivable',
        ]);
    }

    private function equipment(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '1300',
            'type' => AccountType::Asset,
            'name' => 'Equipment',
        ]);
    }

    private function prepaidInsurance(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '1400',
            'type' => AccountType::Asset,
            'name' => 'Prepaid Insurance',
        ]);
    }

    private function accountsPayable(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '2100',
            'type' => AccountType::Liability,
            'name' => 'Accounts Payable',
        ]);
    }

    private function interestPayable(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '2200',
            'type' => AccountType::Liability,
            'name' => 'Interest Payable',
        ]);
    }

    private function revenue(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '4000',
            'type' => AccountType::Revenue,
            'name' => 'Revenue',
        ]);
    }

    private function interestExpense(): Account
    {
        return $this->memoizedAccounts[__FUNCTION__] ??= Account::create([
            'number' => '5100',
            'type' => AccountType::Expense,
            'name' => 'Interest Expense',
        ]);
    }
}
