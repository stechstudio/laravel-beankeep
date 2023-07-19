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
    public function testPostAllowsPostingWhenLineItemsDebitsAndCreditsBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 40000));

        $postSuccess = $transaction->post();

        $this->assertTrue($postSuccess);
        $this->assertTrue($transaction->posted);
    }

    public function testPostAllowsPostingSplitDebits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'pay interest on loan (including accrued interest from prior year)',
        ]);

        $transaction->lineItems()->save($this->debit('interestPayable', 20000));
        $transaction->lineItems()->save($this->debit('interestExpense', 20000));
        $transaction->lineItems()->save($this->credit('cash', 40000));

        $postSuccess = $transaction->post();

        $this->assertTrue($postSuccess);
        $this->assertTrue($transaction->posted);
    }

    public function testPostAllowsPostingSplitCredits(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-03-31'),
            'memo' => 'buy netbook (50% cash, 50% 30-day terms)',
        ]);

        $transaction->lineItems()->save($this->debit('equipment', 40000));
        $transaction->lineItems()->save($this->credit('accountsPayable', 20000));
        $transaction->lineItems()->save($this->credit('cash', 20000));

        $postSuccess = $transaction->post();

        $this->assertTrue($postSuccess);
        $this->assertTrue($transaction->posted);
    }

    public function testPostDisallowsPostingWhenLineItemsDebitsAndCreditsDontBalance(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 30000));

        $postSuccess = $transaction->post();

        $this->assertFalse($postSuccess);
        $this->assertFalse($transaction->posted);
    }

    public function testPostDisallowsPostingWithoutLineItems(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $postSuccess = $transaction->post();

        $this->assertFalse($postSuccess);
        $this->assertFalse($transaction->posted);
    }

    public function testPostRequiresAtLeastOneDebit(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->credit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->credit('revenue', 40000));

        $postSuccess = $transaction->post();

        $this->assertFalse($postSuccess);
        $this->assertFalse($transaction->posted);
    }

    public function testPostRequiresAtLeastOneCredit(): void
    {
        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($this->debit('accountsReceivable', 40000));
        $transaction->lineItems()->save($this->debit('revenue', 40000));

        $postSuccess = $transaction->post();

        $this->assertFalse($postSuccess);
        $this->assertFalse($transaction->posted);
    }

    // ------------------------------------------------------------------------

    private Account $cash;

    private Account $accountsReceivable;

    private Account $equipment;

    private Account $accountsPayable;

    private Account $interestPayable;

    private Account $revenue;

    private Account $interestExpense;

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
        return $this->cash ??= Account::create([
            'number' => '1100',
            'type' => AccountType::Asset,
            'name' => 'Cash',
        ]);
    }

    private function accountsReceivable(): Account
    {
        return $this->accountsRecievable ??= Account::create([
            'number' => '1200',
            'type' => AccountType::Asset,
            'name' => 'Accounts Receivable',
        ]);
    }

    private function equipment(): Account
    {
        return $this->equipment ??= Account::create([
            'number' => '1300',
            'type' => AccountType::Asset,
            'name' => 'Equipment',
        ]);
    }

    private function accountsPayable(): Account
    {
        return $this->accountsPayable ??= Account::create([
            'number' => '2100',
            'type' => AccountType::Liability,
            'name' => 'Accounts Payable',
        ]);
    }

    private function interestPayable(): Account
    {
        return $this->interestPayable ??= Account::create([
            'number' => '2200',
            'type' => AccountType::Liability,
            'name' => 'Interest Payable',
        ]);
    }

    private function revenue(): Account
    {
        return $this->revenue ??= Account::create([
            'number' => '4000',
            'type' => AccountType::Revenue,
            'name' => 'Revenue',
        ]);
    }

    private function interestExpense(): Account
    {
        return $this->interestExpense ??= Account::create([
            'number' => '5100',
            'type' => AccountType::Expense,
            'name' => 'Interest Expense',
        ]);
    }
}
