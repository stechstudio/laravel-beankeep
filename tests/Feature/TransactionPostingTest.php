<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\CanCreateAccounts;

final class TransactionPostingTest extends TestCase
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    public function setUp(): void
    {
        parent::setUp();
        $this->createAccounts();
    }

    // -- ::canPost() --------------------------------------------------

    public function testCanPostReturnsTrueWhenDebitsAndCreditsBalance(): void
    {
        $transaction = $this->thisYear('07/18')->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', cr: 400.00)
            ->draft();

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitDebits(): void
    {
        $transaction = $this->thisYear('03/31')
            ->transact('pay interest on loan (including accrued interest from prior year)')
            ->line('interest-payable', dr: 200.00)
            ->line('interest-expense', dr: 200.00)
            ->line('cash', cr: 400.00)
            ->draft();

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitCredits(): void
    {
        $transaction = $this->thisYear('03/31')
            ->transact('buy netbook (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 400.00)
            ->line('accounts-payable', cr: 200.00)
            ->line('cash', cr: 200.00)
            ->draft();

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitDebitsAndSplitCredits(): void
    {
        $transaction = $this->thisYear('05/05')
            ->transact('buy netbook with extended damage insurance (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 200.00)
            ->line('prepaid-insurance', dr: 200.00)
            ->line('accounts-payable', cr: 200.00)
            ->line('cash', cr: 200.00)
            ->draft();

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenDebitsAndCreditsDontBalance(): void
    {
        $transaction = $this->thisYear('07/18')->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', cr: 300.00)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndCreditDontBalance(): void
    {
        $transaction = $this->thisYear('03/31')
            ->transact('pay interest on loan (including accrued interest from prior year)')
            ->line('interest-payable', dr: 200.00)
            ->line('interest-expense', dr: 200.00)
            ->line('cash', cr: 400.02)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenDebitAndSplitCreditsDontBalance(): void
    {
        $transaction = $this->thisYear('03/31')
            ->transact('buy netbook (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 400.00)
            ->line('accounts-payable', cr: 200.00)
            ->line('cash', cr: 199.99)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndSplitCreditDontBalance(): void
    {
        $transaction = $this->thisYear('05/05')
            ->transact('buy netbook with extended damage insurance (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 200.00)
            ->line('prepaid-insurance', dr: 200.00)
            ->line('accounts-payable', cr: 200.10)
            ->line('cash', cr: 200.00)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutLineItems(): void
    {
        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyDebits(): void
    {
        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', cr: 400.00)
            ->line('revenue', cr: 400.00)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyCredits(): void
    {
        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', dr: 400.00)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    // -- post via ::save() ---------------------------------------------------

    public function testSaveAllowsPostingWhenLineItemsDebitsAndCreditsBalance(): void
    {
        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', cr: 400.00)
            ->draft();

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

        $transaction->lineItems()->save($this->debit('interest-payable', 20000));
        $transaction->lineItems()->save($this->debit('interest-expense', 20000));
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
        $transaction->lineItems()->save($this->credit('accounts-payable', 20000));
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
        $transaction->lineItems()->save($this->debit('prepaid-insurance', 20000));
        $transaction->lineItems()->save($this->credit('accounts-payable', 20000));
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

        $transaction->lineItems()->save($this->debit('accounts-receivable', 40000));
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

        $transaction->lineItems()->save($this->debit('accounts-receivable', 40000));
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

        $transaction->lineItems()->save($this->debit('interest-payable', 20000));
        $transaction->lineItems()->save($this->debit('interest-expense', 20000));
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
        $transaction->lineItems()->save($this->credit('accounts-payable', 20000));
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
        $transaction->lineItems()->save($this->debit('prepaid-insurance', 20000));
        $transaction->lineItems()->save($this->credit('accounts-payable', 20010));
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

        $transaction->lineItems()->save($this->credit('accounts-receivable', 40000));
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

        $transaction->lineItems()->save($this->debit('accounts-receivable', 40000));
        $transaction->lineItems()->save($this->debit('revenue', 40000));

        $transaction->posted = true;

        $this->assertFalse($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    // ------------------------------------------------------------------------

    private function debit(string $account, int $amount): LineItem
    {
        $debit = new LineItem(['debit' => $amount, 'credit' => 0]);
        $debit->account()->associate($this->account($account));

        return $debit;
    }

    private function credit(string $account, int $amount): LineItem
    {
        $credit = new LineItem(['debit' => 0, 'credit' => $amount]);
        $credit->account()->associate($this->account($account));

        return $credit;
    }
}
