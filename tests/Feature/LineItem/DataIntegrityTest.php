<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\LineItem;

use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Exceptions\LineItemInvalid;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Tests\TestCase;

final class DataIntegrityTest extends TestCase
{
    public function testCreatingWithJustADebitSucceeds(): void
    {
        $lineItem = LineItem::create([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'debit' => 10000,
        ]);

        $this->assertTrue($lineItem->exists);
        $this->assertTrue($lineItem->isDebit());
        $this->assertFalse($lineItem->isCredit());
    }

    public function testCreatingWithJustACreditSucceeds(): void
    {
        $lineItem = LineItem::create([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'credit' => 10000,
        ]);

        $this->assertTrue($lineItem->exists);
        $this->assertFalse($lineItem->isDebit());
        $this->assertTrue($lineItem->isCredit());
    }

    public function testSavingWithJustADebitSucceeds(): void
    {
        $lineItem = new LineItem([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'debit' => 10000,
        ]);

        $this->assertTrue($lineItem->save());
        $this->assertTrue($lineItem->isDebit());
        $this->assertFalse($lineItem->isCredit());
    }

    public function testSavingWithJustACreditSucceeds(): void
    {
        $lineItem = new LineItem([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'credit' => 10000,
        ]);

        $this->assertTrue($lineItem->save());
        $this->assertFalse($lineItem->isDebit());
        $this->assertTrue($lineItem->isCredit());
    }

    public function testRefusesToCreateWithBothCreditAndDebitAmount(): void
    {
        $this->expectException(LineItemInvalid::class);

        $lineItem = LineItem::create([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'debit' => 10000,
            'credit' => 10000,
        ]);
    }

    public function testRefusesToSaveWithBothCreditAndDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 10000, 'credit' => 10000]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->expectException(LineItemInvalid::class);

        $lineItem->save();
    }

    public function testRefusesToUpdateWithBothCreditAndDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 10000, 'credit' => 0]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->assertTrue($lineItem->save());
        $this->assertTrue($lineItem->exists);

        $lineItem->credit = 10000;

        $this->expectException(LineItemInvalid::class);

        $lineItem->save();
    }

    public function testRefusesToCreateWithoutEitherCreditOrDebitAmount(): void
    {
        $this->expectException(LineItemInvalid::class);

        $lineItem = LineItem::create([
            'account_id' => $this->account()->id,
            'transaction_id' => $this->transaction()->id,
            'debit' => 0,
            'credit' => 0,
        ]);
    }

    public function testRefusesToSaveWithoutEitherCreditOrDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 0, 'credit' => 0]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->expectException(LineItemInvalid::class);

        $lineItem->save();
    }

    public function testRefusesToUpdateWithoutEitherCreditOrDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 10000, 'credit' => 0]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->assertTrue($lineItem->save());
        $this->assertTrue($lineItem->exists);

        $lineItem->debit = 0;

        $this->expectException(LineItemInvalid::class);

        $lineItem->save();
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
