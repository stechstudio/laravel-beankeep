<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Support\Collection;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

final class Ledger
{
    /** @var Collection<LineItem> */
    private Collection $debits;

    /** @var Collection<LineItem> */
    private Collection $credits;

    /**
     * @param Collection<LineItem> $ledgerEntries
     */
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
        return $this->isDebitPositive()
            ? $this->debitPositiveBalance()
            : $this->creditPositiveBalance();
    }

    private function isDebitPositive(): bool
    {
        return match ($this->account->type) {
            AccountType::Asset => true,
            AccountType::Expense => true,
            default => false,
        };
    }

    private function isCreditPositive(): bool
    {
        return !$this->isDebitPositive();
    }

    private function debitPositiveBalance(): int
    {
        return $this->startingBalance
            + $this->debits->sum('debit')
            - $this->credits->sum('credit');
    }

    private function creditPositiveBalance(): int
    {
        return $this->startingBalance
            + $this->credits->sum('credit')
            - $this->debits->sum('debit');
    }
}
