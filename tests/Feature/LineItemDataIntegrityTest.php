<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Tests\TestCase;

final class LineItemDataIntegrityTest extends TestCase
{
    public function testRefusesToCreateWithBothCreditAndDebitAmount(): void
    {
        $lineItem = LineItem::create([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'debit' => 10000,
            'credit' => 10000,
        ]);

        $this->assertFalse($lineItem->exists);
    }

    public function testRefusesToSaveWithBothCreditAndDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 10000, 'credit' => 10000]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->assertFalse($lineItem->save());
        $this->assertFalse($lineItem->exists);
    }

    public function testRefusesToCreateWithoutEitherCreditOrDebitAmount(): void
    {
        $lineItem = LineItem::create([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'debit' => 0,
            'credit' => 0,
        ]);

        $this->assertFalse($lineItem->exists);
    }

    public function testRefusesToSaveWithoutEitherCreditOrDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 0, 'credit' => 0]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->assertFalse($lineItem->save());
        $this->assertFalse($lineItem->exists);
    }

    public function testRefusesToUpdateWithoutEitherCreditOrDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 10000, 'credit' => 0]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->assertTrue($lineItem->save());
        $this->assertTrue($lineItem->exists);

        $lineItem->debit = 0;

        $this->assertFalse($lineItem->save());
        $this->assertEquals(10000, $lineItem->refresh()->debit);
    }

    // ------------------------------------------------------------------------

    private Account $account;

    private Transaction $transaction;

    private function account(): Account
    {
        return $account ??= Account::create([
            'number' => '1100',
            'type' => AccountType::Asset,
            'name' => 'Cash',
        ]);
    }

    private function transaction(): Transaction
    {
        return $transaction ??= Transaction::create([
            'date' => Carbon::parse('2023-07-18'),
            'memo' => 'perform services',
        ]);
    }
}
