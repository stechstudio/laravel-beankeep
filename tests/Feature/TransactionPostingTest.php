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
        $debit = $this->debit('accountsReceivable', 40000);
        $credit = $this->credit('revenue', 40000);

        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($debit);
        $transaction->lineItems()->save($credit);

        $postSuccess = $transaction->post();

        $this->assertTrue($postSuccess);
        $this->assertTrue($transaction->posted);
    }

    public function testPostDisallowsPostingWhenLineItemsDebitsAndCreditsDontBalance(): void
    {
        $debit = $this->debit('accountsReceivable', 40000);
        $credit = $this->credit('revenue', 30000);

        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($debit);
        $transaction->lineItems()->save($credit);

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
        $credit1 = $this->credit('accountsReceivable', 40000);
        $credit2 = $this->credit('revenue', 40000);

        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($credit1);
        $transaction->lineItems()->save($credit2);

        $postSuccess = $transaction->post();

        $this->assertFalse($postSuccess);
        $this->assertFalse($transaction->posted);
    }

    public function testPostRequiresAtLeastOneCredit(): void
    {
        $debit1 = $this->debit('accountsReceivable', 40000);
        $debit2 = $this->debit('revenue', 40000);

        $transaction = Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $transaction->lineItems()->save($debit1);
        $transaction->lineItems()->save($debit2);

        $postSuccess = $transaction->post();

        $this->assertFalse($postSuccess);
        $this->assertFalse($transaction->posted);
    }

    // ------------------------------------------------------------------------

    private Account $accountsReceivable;

    private Account $revenue;

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

    private function accountsReceivable(): Account
    {
        return $this->accountsRecievable ??= Account::create([
            'number' => '1200',
            'type' => AccountType::Asset,
            'name' => 'Accounts Receivable',
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
}
