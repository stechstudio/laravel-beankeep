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
        $this->seedAccountsIfNeeded();

        Transaction::factory()
            ->count(100)->create()
            ->each(function (Transaction $transaction) {
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

    protected function seedAccountsIfNeeded(): void
    {
        if (!Account::count()) {
            $this->call([AccountSeeder::class]);
        }
    }

    protected function randomUnsavedLineItems(): array
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

    protected function amount(): int
    {
        return $this->faker->numberBetween(1, 1500) * 100;
    }

    protected function shouldHaveSourceDocument(): bool
    {
        return $this->fiftyFifty();
    }

    protected function shouldPostTransaction(): bool
    {
        return $this->fourTimesOutOfFive();
    }

    protected function fiftyFifty(): bool
    {
        return (bool) $this->faker->numberBetween(0, 1);
    }

    protected function fourTimesOutOfFive(): bool
    {
        return $this->faker->numberBetween(1, 5) % 5 != 0;
    }
}
