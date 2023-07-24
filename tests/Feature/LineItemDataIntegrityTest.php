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
    public function testRefusesToSaveWithBothCreditAndDebitAmount(): void
    {
        $lineItem = new LineItem(['debit' => 10000, 'credit' => 10000]);
        $lineItem->account_id = $this->account()->id;
        $lineItem->transaction_id = $this->transaction()->id;

        $this->assertFalse($lineItem->save());
        $this->assertFalse($lineItem->exists);
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
