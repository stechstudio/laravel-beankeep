<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Carbon\Carbon;
use Closure;
use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Database\Factories\Support\AccountLookup;
use STS\Beankeep\Enums\JournalPeriod;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Journal;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\Transaction;
use ValueError;

trait GeneratesJournalData
{
    protected function txn(
        string $date,
        ?array $dr = null,
        ?array $cr = null,
        bool $posted = true,
    ): Transaction {
        $this->for('jan', function (Closure $txn, Closure $draftTxn) use (
            $date,
            $dr,
            $cr,
            $posted,
            &$transaction,
        ) {
            $transaction = $posted
                ? $txn($date, $dr, $cr)
                : $draftTxn($date, $dr, $cr);
        });

        return $transaction;
    }

    protected function draft(
        string $date,
        array $dr,
        array $cr,
    ): Transaction {
        return $this->txn($date, $dr, $cr, false);
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

    protected function lineItemValues(?array $dr, ?array $cr): array
    {
        if (is_null($dr) || is_null($cr)) {
            throw new ValueError('Supply both a debit an a credit please.');
        }

        if (is_string($dr[0])) {
            [$debitAccount, $debitAmount] = $dr;
        } else {
            [$debitAmount, $debitAccount] = $dr;
        }

        if (is_string($cr[0])) {
            [$creditAccount, $creditAmount] = $cr;
        } else {
            [$creditAmount, $creditAccount] = $cr;
        }

        return [$debitAccount, $debitAmount, $creditAccount, $creditAmount];
    }

    protected function getTransactionConstructors(
        Journal $journal,
        array $accounts,
    ): array {
        $txn = function (
            string $date,
            array $dr,
            array $cr,
            bool $posted = true,
        ) use ($journal, $accounts): Transaction {
            [
                $debitAccount,
                $debitAmount,
                $creditAccount,
                $creditAmount,
            ] = $this->lineItemValues($dr, $cr);

            $transaction = Transaction::factory()->create([
                'date' => Carbon::parse($date),
            ]);

            $debit = LineItem::factory()->make([
                'debit' => (int) ($debitAmount * 100),
                'credit' => 0,
                'account_id' => $accounts[$debitAccount]->id,
            ]);

            $credit = LineItem::factory()->make([
                'debit' => 0,
                'credit' => (int) ($creditAmount * 100),
                'account_id' => $accounts[$creditAccount]->id,
            ]);

            $transaction->lineItems()->save($debit);
            $transaction->lineItems()->save($credit);

            if ($posted) {
                $transaction->posted = true;
                $transaction->save();
            }

            return $transaction;
        };

        $draftTxn = function (
            string $date,
            array $dr,
            array $cr,
        ) use ($txn): Transaction {
            return $txn($date, $dr, $cr, false);
        };

        return [$txn, $draftTxn];
    }
}
