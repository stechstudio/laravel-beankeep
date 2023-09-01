<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;
use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Database\Factories\Support\AccountLookup;
use STS\Beankeep\Enums\JournalPeriod;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Journal;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\Transaction;

trait GeneratesJournalData
{
    protected function txn(
        string $date,
        ?Closure $cb = null,
        ?array $dr = null,
        ?array $cr = null,
        bool $posted = true,
    ): Transaction {
        $this->for('jan', function (Closure $txn, Closure $draft) use (
            $date,
            $cb,
            $dr,
            $cr,
            $posted,
            &$transaction,
        ) {
            $transaction = $posted
                ? $txn($date, $cb, $dr, $cr)
                : $draft($date, $cb, $dr, $cr);
        });

        return $transaction;
    }

    protected function draft(
        string $date,
        ?Closure $cb = null,
        ?array $dr = null,
        ?array $cr = null,
    ): Transaction {
        return $this->txn($date, $cb, $dr, $cr, false);
    }

    protected function for(
        JournalPeriod|string $journalPeriod,
        Closure $cb,
    ): array {
        return $this->forJournal(1, $journalPeriod, $cb);
    }

    protected function forJournal(
        int $journalId,
        JournalPeriod|string $journalPeriod,
        Closure $cb,
    ): array {
        $journal = $this->getJournal(1, $journalPeriod);
        $accounts = $this->getAccounts($journal);

        [$txn, $draftTxn] = $this->getTransactionConstructors(
            $journal,
            $accounts,
        );

        $cb($txn, $draftTxn);

        return [$journal, $accounts];
    }

    protected function getJournal(
        int $journalId,
        JournalPeriod|string|null $journalPeriod = null,
    ): Journal {
        $journalPeriod = $this->journalPeriod($journalPeriod);

        return Journal::updateOrCreate(
            ['id' => $journalId],
            ['period' => $this->journalPeriod($journalPeriod)],
        );
    }

    protected function journalPeriod(
        JournalPeriod|string|null $journalPeriod = null,
    ): JournalPeriod {
        if (is_string($journalPeriod)) {
            return JournalPeriod::fromString($journalPeriod);
        }

        return $journalPeriod ?? JournalPeriod::Jan;
    }

    protected function createAccounts(
        ?Journal $journal = null,
        JournalPeriod|string|null $journalPeriod = null,
    ): array {
        return $this->getAccounts($journal, $journalPeriod);
    }

    protected function getAccounts(
        ?Journal $journal = null,
        JournalPeriod|string|null $journalPeriod = null,
    ): array {
        $journal ??= $this->getJournal(1, $journalPeriod);

        if (! $journal->accounts()->exists()) {
            Account::factory()
                ->for($journal)
                ->createMany(AccountFactory::defaultAccountAttributes());
        }

        return AccountLookup::lookupTable($journal);
    }

    protected function makeLineItems(
        array $accounts,
        ?Closure $cb,
        ?array $dr,
        ?array $cr,
    ): Collection {
        $lineItems = collect();

        $debit = function (
            string $account,
            float $amount,
        ) use ($accounts, $lineItems) {
            $lineItems->push(LineItem::factory()->make([
                'debit' => (int) ($amount * 100),
                'credit' => 0,
                'account_id' => $accounts[$account]->id,
            ]));
        };

        $credit = function (
            string $account,
            float $amount,
        ) use ($accounts, $lineItems) {
            $lineItems->push(LineItem::factory()->make([
                'debit' => 0,
                'credit' => (int) ($amount * 100),
                'account_id' => $accounts[$account]->id,
            ]));
        };

        if ($dr) {
            $debit($dr[0], $dr[1]);
        }

        if ($cr) {
            $credit($cr[0], $cr[1]);
        }

        if ($cb) {
            $cb($debit, $credit);
        }

        return $lineItems;
    }

    protected function getTransactionConstructors(
        Journal $journal,
        array $accounts,
    ): array {
        $txn = function (
            string $date,
            ?Closure $cb = null,
            ?array $dr = null,
            ?array $cr = null,
            bool $posted = true,
        ) use ($journal, $accounts): Transaction {
            $lineItems = $this->makeLineItems($accounts, $cb, $dr, $cr);

            $transaction = Transaction::factory()->create([
                'date' => Carbon::parse($date),
            ]);

            $lineItems->each(fn ($lineItem) =>
                $transaction->lineItems()->save($lineItem));

            if ($posted) {
                $transaction->posted = true;
                $transaction->save();
            }

            return $transaction;
        };

        $draft = function (
            string $date,
            ?Closure $cb = null,
            ?array $dr = null,
            ?array $cr = null,
        ) use ($txn): Transaction {
            return $txn($date, $cb, $dr, $cr, false);
        };

        return [$txn, $draft];
    }
}
