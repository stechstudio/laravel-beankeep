<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Closure;
use STS\Beankeep\Enums\JournalPeriod;
use STS\Beankeep\Models\Journal;
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
        ): Transaction {
            $transaction = $posted
                ? $txn($date, $dr, $cr)
                : $draftTxn($date, $dr, $cr);
        });

        return $transaction;
    }

    protected function draftTxn(
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
        $journal = Journal::updateOrCreate(
            ['id' => $journalId],
            ['period' => $this->journalPeriod($journalPeriod)],
        );

        // TODO(zmd): create accounts (if necessary) for real
        $accounts = [];

        $txn = function (
            string $date,
            array $dr,
            array $cr,
            bool $posted = false,
        ) use ($journal, $accounts): Transaction {
            [
                $debitAccount,
                $debitAmount,
                $creditAccount,
                $creditAmount,
            ] = $this->lineItemValues($dr, $cr);

            $debitAccount = $accounts[$debitAccount];
            $creditAccount = $accounts[$creditAccount];

            // TODO(zmd): finish implementing me for real

        };

        $draftTxn = function (
            string $date,
            array $dr,
            array $cr,
        ) use ($txn): Transaction {
            return $txn($date, $dr, $cr, false);
        };

        $cb($txn, $draftTxn);

        return [$journal, $accounts];
    }

    protected function journalPeriod(
        JournalPeriod|string $journalPeriod,
    ): JournalPeriod {
        if (is_string($journalPeriod)) {
            return JournalPeriod::fromString($journalPeriod);
        }

        return $journalPeriod;
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
}
