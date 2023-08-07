<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

// TODO(zmd): rename to Ledger (replacing current Ledger class) if this idea
//   sticks
final class LedgerCollection extends Collection
{
    /**
     * @param LineItem[] $models
     */
    public function __construct(
        array $models,
        private Account $account,
        private int $startingBalance,
    ) {
        parent::__construct($models);
    }

    public function debits(): Collection
    {
        $this->filter->isDebit();
    }

    public function credits(): Collection
    {
        $this->filter->isCredit();
    }

    public function balance(): int
    {
        return $this->isDebitPositive()
            ? $this->debitPositiveBalance()
            : $this->creditPositiveBalance();
    }

    // TODO(zmd): these should be public instance methods on the Account model
    //   itself
    private function isDebitPositive(): bool
    {
        return match ($this->account->type) {
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
        return $this->startingBalance
            + $this->debits()->sum('debit')
            - $this->credits()->sum('credit');
    }

    private function creditPositiveBalance(): int
    {
        return $this->startingBalance
            + $this->credits()->sum('credit')
            - $this->debits()->sum('debit');
    }
}
