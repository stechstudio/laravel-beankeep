<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Transaction;

use Illuminate\Support\Carbon;
use STS\Beankeep\Exceptions\TransactionLineItemsMissing;
use STS\Beankeep\Exceptions\TransactionLineItemsUnbalanced;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class PostingTest extends TestCase
{
    use GeneratesJournalData;

    // -- ::canPost() ---------------------------------------------------------

    public function testCanPostReturnsTrueWhenDebitsAndCreditsBalance(): void
    {
        $transaction = $this->draft(
            '07/18/2023',
            dr: ['accounts-receivable', 400.00],
            cr: ['revenue', 400.00],
        );

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitDebits(): void
    {
        $transaction = $this->draft('03/31', function ($debit, $credit) {
            $debit('interest-payable', 200.00);
            $debit('interest-expense', 200.00);
            $credit('cash', 400.00);
        });

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitCredits(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', cr: 300.00)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndCreditDontBalance(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyDebits(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', cr: 400.00)
            ->line('revenue', cr: 400.00)
            ->draft();

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyCredits(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

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
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('03/31')
            ->transact('pay interest on loan (including accrued interest from prior year)')
            ->line('interest-payable', dr: 200.00)
            ->line('interest-expense', dr: 200.00)
            ->line('cash', cr: 400.00)
            ->draft();

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitCredits(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('03/31')
            ->transact('buy netbook (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 400.00)
            ->line('accounts-payable', cr: 200.00)
            ->line('cash', cr: 200.00)
            ->draft();

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitDebitsWithSplitCredits(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('05/05')
            ->transact('buy netbook with extended damage insurance (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 200.00)
            ->line('prepaid-insurance', dr: 200.00)
            ->line('accounts-payable', cr: 200.00)
            ->line('cash', cr: 200.00)
            ->draft();

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowedForUnbalancedLineItemsAsLongAsPostedRemainsFalse(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', 400.00)
            ->line('revenue', 300.00)
            ->draft();

        $transaction->memo = 'perform PREMIUM services';

        $this->assertTrue($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWhenLineItemsDebitsAndCreditsDontBalance(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', cr: 300.00)
            ->draft();

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWhenLineItemsSplitDebitsAndCreditDontBalance(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('03/31')
            ->transact('pay interest on loan (including accrued interest from prior year)')
            ->line('interest-payable', dr: 200.00)
            ->line('interest-expense', dr: 200.00)
            ->line('cash', cr: 400.02)
            ->draft();

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWhenLineItemsDebitAndSplitCreditsDontBalance(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('03/31')
            ->transact('buy netbook (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 400.00)
            ->line('accounts-payable', cr: 200.00)
            ->line('cash', cr: 199.99)
            ->draft();

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWhenLineItemsSplitDebitsAndSplitCreditDontBalance(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('05/05')
            ->transact('buy netbook with extended damage insurance (50% cash, 50% 30-day terms)')
            ->line('equipment', dr: 200.00)
            ->line('prepaid-insurance', dr: 200.00)
            ->line('accounts-payable', cr: 200.10)
            ->line('cash', cr: 200.00)
            ->draft();

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWithoutLineItems(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->draft();

        $this->expectException(TransactionLineItemsMissing::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveNewDisallowsPostingBecauseNoLineItemsArePossiblyAssociatedYet(): void
    {
        $transaction = new Transaction([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);

        $this->expectException(TransactionLineItemsMissing::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveRequiresAtLeastOneDebit(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', cr: 400.00)
            ->line('revenue', cr: 400.00)
            ->draft();

        $this->expectException(TransactionLineItemsMissing::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveRequiresAtLeastOneCredit(): void
    {
        $this->markTestSkipped('TODO(zmd): revision needed');

        $transaction = $this->thisYear('07/18')
            ->transact('perform services')
            ->line('accounts-receivable', dr: 400.00)
            ->line('revenue', dr: 400.00)
            ->draft();

        $this->expectException(TransactionLineItemsMissing::class);

        $transaction->posted = true;
        $transaction->save();
    }
}
