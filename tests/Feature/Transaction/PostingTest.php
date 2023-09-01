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
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('interest-payable', 200.00);
            $debit('interest-expense', 200.00);
            $credit('cash', 400.00);
        });

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitCredits(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('equipment', 400.00);
            $credit('accounts-payable', 200.00);
            $credit('cash', 200.00);
        });

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsTrueWithSplitDebitsAndSplitCredits(): void
    {
        $transaction = $this->draft('5/5/2023', function ($debit, $credit) {
            $debit('equipment', 200.00);
            $debit('prepaid-insurance', 200.00);
            $credit('accounts-payable', 200.00);
            $credit('cash', 200.00);
        });

        $this->assertTrue($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenDebitsAndCreditsDontBalance(): void
    {
        $transaction = $this->draft('7/18/2023', function ($debit, $credit) {
            $debit('accounts-receivable', 400.00);
            $credit('revenue', 300.00);
        });

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndCreditDontBalance(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('interest-payable', 200.00);
            $debit('interest-expense', 200.00);
            $credit('cash', 400.02);
        });

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenDebitAndSplitCreditsDontBalance(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('equipment', 400.00);
            $credit('accounts-payable', 200.00);
            $credit('cash', 199.99);
        });

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWhenSplitDebitsAndSplitCreditDontBalance(): void
    {
        $transaction = $this->draft('05/05/2023', function ($debit, $credit) {
            $debit('equipment', 200.00);
            $debit('prepaid-insurance', 200.00);
            $credit('accounts-payable', 200.10);
            $credit('cash', 200.00);
        });

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutLineItems(): void
    {
        $transaction = $this->draft('7/18/2023', function ($_debit, $_credit) {});

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyDebits(): void
    {
        $transaction = $this->draft('7/18/2023', function ($_debit, $credit) {
            $credit('accounts-receivable', 400.00);
            $credit('revenue', 400.00);
        });

        $this->assertFalse($transaction->canPost());
    }

    public function testCanPostReturnsFalseWithoutAnyCredits(): void
    {
        $transaction = $this->draft('7/18/2023', function ($debit, $_credit) {
            $debit('accounts-receivable', 400.00);
            $debit('revenue', 400.00);
        });

        $this->assertFalse($transaction->canPost());
    }

    // -- post via ::save() ---------------------------------------------------

    public function testSaveAllowsPostingWhenLineItemsDebitsAndCreditsBalance(): void
    {
        $transaction = $this->draft('7/18/2023', function ($debit, $credit) {
            $debit('accounts-receivable', 400.00);
            $credit('revenue', 400.00);
        });

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitDebits(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('interest-payable', 200.00);
            $debit('interest-expense', 200.00);
            $credit('cash', 400.00);
        });

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitCredits(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('equipment', 400.00);
            $credit('accounts-payable', 200.00);
            $credit('cash', 200.00);
        });

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowsPostingSplitDebitsWithSplitCredits(): void
    {
        $transaction = $this->draft('5/5/2023', function ($debit, $credit) {
            $debit('equipment', 200.00);
            $debit('prepaid-insurance', 200.00);
            $credit('accounts-payable', 200.00);
            $credit('cash', 200.00);
        });

        $transaction->posted = true;

        $this->assertTrue($transaction->save());
        $this->assertTrue($transaction->refresh()->posted);
    }

    public function testSaveAllowedForUnbalancedLineItemsAsLongAsPostedRemainsFalse(): void
    {
        $transaction = $this->draft('7/18/2023', function ($debit, $credit) {
            $debit('accounts-receivable', 400.00);
            $credit('revenue', 300.00);
        });

        $transaction->memo = 'perform PREMIUM services';

        $this->assertTrue($transaction->save());
        $this->assertFalse($transaction->refresh()->posted);
    }

    public function testSaveDisallowsPostingWhenLineItemsDebitsAndCreditsDontBalance(): void
    {
        $transaction = $this->draft('7/18/2023', function ($debit, $credit) {
            $debit('accounts-receivable', 400.00);
            $credit('revenue', 300.00);
        });

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWhenLineItemsSplitDebitsAndCreditDontBalance(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('interest-payable', 200.00);
            $debit('interest-expense', 200.00);
            $credit('cash', 400.02);
        });

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWhenLineItemsDebitAndSplitCreditsDontBalance(): void
    {
        $transaction = $this->draft('3/31/2023', function ($debit, $credit) {
            $debit('equipment', 400.00);
            $credit('accounts-payable', 200.00);
            $credit('cash', 199.99);
        });

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWhenLineItemsSplitDebitsAndSplitCreditDontBalance(): void
    {
        $transaction = $this->draft('5/5/2023', function ($debit, $credit) {
            $debit('equipment', 200.00);
            $debit('prepaid-insurance', 200.00);
            $credit('accounts-payable', 200.10);
            $credit('cash', 200.00);
        });

        $this->expectException(TransactionLineItemsUnbalanced::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveDisallowsPostingWithoutLineItems(): void
    {
        $transaction = $this->draft('07/18', function ($_debit, $_credit) {});

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
        $transaction = $this->draft('7/18/2023', function ($_debit, $credit) {
            $credit('accounts-receivable', 400.00);
            $credit('revenue', 400.00);
        });

        $this->expectException(TransactionLineItemsMissing::class);

        $transaction->posted = true;
        $transaction->save();
    }

    public function testSaveRequiresAtLeastOneCredit(): void
    {
        $transaction = $this->draft('7/18/2023', function ($debit, $_credit) {
            $debit('accounts-receivable', 400.00);
            $debit('revenue', 400.00);
        });

        $this->expectException(TransactionLineItemsMissing::class);

        $transaction->posted = true;
        $transaction->save();
    }
}
