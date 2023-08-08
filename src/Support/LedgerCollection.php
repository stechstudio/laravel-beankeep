<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

// TODO(zmd): rename to Ledger (replacing current Ledger class) if this idea
//   sticks
class LedgerCollection extends LineItemCollection
{
    private readonly Account $account;

    private readonly int $startingBalance;

    public function balance(): int
    {
        return $this->isDebitPositive()
            ? $this->debitPositiveBalance()
            : $this->creditPositiveBalance();
    }

    public function getAccount(): Account
    {
        return $this->account ?? $this->first()->account;
    }

    public function getStartingBalance(): int
    {
        return $this->startingBalance ??= (function () {
            $earlierLineItems = $this->getAccount()
                ->lineItems()
                ->priorTo($this->getStartDate())
                ->get();

            if ($this->debitPositiveBalance()) {
                return self::calcDebitPositiveBalance(
                    $earlierLineItems->debit(),
                    $earlierLineItems->credit(),
                    0,
                );
            }

            return self::calcCreditPositiveBalance(
                $earlierLineItems->debit(),
                $earlierLineItems->credit(),
                0,
            );
        })();
    }

    public function getStartDate(): Carbon
    {
        return $this->sortBy(fn (LineItem $item, int $key) =>
                $item->transaction->date)
            ->first()
            ->transaction->date;
    }

    public static function calcDebitPositiveBalance(
        LineItemCollection $debits,
        LineItemCollection $credits,
        int $startingBalance,
    ): int {
        return $startingBalances
            + $debits->sum('debit')
            - $credits->sum('credit');
    }

    public static function calcCreditPositiveBalance(
        LineItemCollection $debits,
        LineItemCollection $credits,
        int $startingBalance,
    ): int {
        return $startingBalances
            + $credits->sum('credit')
            - $debits->sum('debit');
    }

    // TODO(zmd): these should be public instance methods on the Account model
    //   itself
    private function isDebitPositive(): bool
    {
        return match ($this->getAccount()->type) {
            AccountType::Asset => true,
            AccountType::Expense => true,
            default => false,
        };
    }

    // TODO(zmd): these should be public instance methods on the Account model
    //   itself
    private function isCreditPositive(): bool
    {
        return !$this->isDebitPositive();
    }

    private function debitPositiveBalance(): int
    {
        return self::calcDebitPositiveBalance(
            $this->debits(),
            $this->credits(),
            $this->getStartingBalance(),
        );
    }

    private function creditPositiveBalance(): int
    {
        return self::calcCreditPositiveBalance(
            $this->debits(),
            $this->credits(),
            $this->getStartingBalance(),
        );
    }
}
