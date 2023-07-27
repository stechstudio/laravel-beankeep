<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

class RandomSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,
        ]);

        Transaction::factory()->count(100)->create();

        Transaction::all()->each(function (Transaction $transaction) {
            foreach ($this->randomUnsavedLineItems() as $lineItem) {
                $transaction->lineItems()->save($lineItem);
            }

            if ($this->shouldHaveSourceDocument()) {
                $sourceDocument = SourceDocument::factory()->make();
                $transaction->sourceDocuments()->save($sourceDocument);
            }

            if ($this->shouldPostTransaction()) {
                $transaction->posted = true;
                $transaction->save();
            }
        });
    }

    public function randomUnsavedLineItems(): array
    {
        $accounts = Account::all();

        [$debitAccount, $creditAccount] = $accounts->random(2);
        $amount = $this->amount();

        $debitLineItem = LineItem::factory()->make([ 'debit' => $amount ]);
        $debitLineItem->account()->associate($debitAccount);

        $creditLineItem = LineItem::factory()->make([ 'credit' => $amount ]);
        $creditLineItem->account()->associate($creditAccount);

        return [$debitLineItem, $creditLineItem];
    }

    public function amount(): int
    {
        return $this->faker->numberBetween(1, 1500) * 100;
    }

    public function shouldHaveSourceDocument(): bool
    {
        return $this->faker->numberBetween(1, 5) % 5 != 0;
    }

    public function shouldPostTransaction(): bool
    {
        return $this->faker->numberBetween(1, 5) % 5 == 0;
    }
}
