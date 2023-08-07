<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Support\Collection;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

final class Ledger
{
    private Collection $debits;

    private Collection $credits;

    public function __construct(
        private Account $account,
        private int $startingBalance,
        Collection $ledgerEntries,
    ) {
        $this->debits = $ledgerEntries->filter->isDebit();
        $this->credits = $ledgerEntries->filter->isCredit();
    }

    public function balance(): int
    {
        if ($this->isDebitPositive()) {
            return $this->startingBalance
                + $this->debits->sum('debit')
                - $this->credits->sum('credit');
        }

        // TODO(zmd): implement me
        return 0;
    }

    public function isDebitPositive(): bool
    {
        return match ($this->account->type) {
            AccountType::Asset => true,
            AccountType::Expense => true,
            default => false,
        };
    }

    public function isCreditPositive(): bool
    {
        return !$this->isDebitPositive();
    }
}
