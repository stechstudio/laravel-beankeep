<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Carbon\CarbonPeriod;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Database\Factories\Support\Transactor;
use STS\Beankeep\Models\Transaction;
use ValueError;

trait HasTransactionMakingShortcuts
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    protected bool $quickAccountsAlreadyInitialized = false;

    protected function txn(
        ?string $thisYear = null,
        ?string $lastYear = null,
        ?array $dr = null,
        ?array $cr = null,
        bool $posted = true,
    ): Transaction {
        if ($posted) {
            return $this->quickTransaction(
                $this->getQuickLineItemValues($dr, $cr),
                $thisYear,
                $lastYear,
            )->post();
        } else {
            return $this->quickTransaction(
                $this->getQuickLineItemValues($dr, $cr),
                $thisYear,
                $lastYear,
            )->draft();
        }
    }

    protected function draftTxn(
        ?string $thisYear = null,
        ?string $lastYear = null,
        ?array $dr = null,
        ?array $cr = null,
    ): Transaction {
        return $this->txn($thisYear, $lastYear, $dr, $cr, false);
    }

    protected function quickTransaction(
        array $debitAndCreditInfo,
        ?string $thisYear = null,
        ?string $lastYear = null,
    ): Transactor {
        $transactor = $this->getQuickTransactor($thisYear, $lastYear);

        [
            $debitAccount,
            $debitAmount,
            $creditAccount,
            $creditAmount,
        ] = $debitAndCreditInfo;

        return $transactor->transact()
            ->line($debitAccount, dr: $debitAmount)
            ->line($creditAccount, cr: $creditAmount);
    }

    protected function getQuickLineItemValues(?array $dr, ?array $cr): array
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

    protected function getQuickTransactor(
        ?string $thisYear,
        ?string $lastYear,
    ): Transactor {
        if (is_null($thisYear) && is_null($lastYear)) {
            throw new ValueError('Pick a date from last year OR this year please.');
        }

        return $thisYear
            ? $this->thisYear($thisYear)
            : $this->lastYear($lastYear);
    }

    protected function janPeriod(): CarbonPeriod
    {
        $start = $this->getDate(thisYear: '1/1');
        $end = $start->endOfMonth();

        return $start->daysUntil($end);
    }

    protected function febPeriod(): CarbonPeriod
    {
        $start = $this->getDate(thisYear: '2/1');
        $end = $start->endOfMonth();

        return $start->dayUntil($end);
    }
}
