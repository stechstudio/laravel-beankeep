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
        return $this->startingBalance ?? (function () {
            // TODO(zmd): work out starting balance based on selected line
            //   items of this collection
        })();
    }

    public function getStartDate(): Carbon
    {
        // TODO(zmd): implement me
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
        return $this->getStartingBalance()
            + $this->debits()->sum('debit')
            - $this->credits()->sum('credit');
    }

    private function creditPositiveBalance(): int
    {
        return $this->getStartingBalance
            + $this->credits()->sum('credit')
            - $this->debits()->sum('debit');
    }
}
