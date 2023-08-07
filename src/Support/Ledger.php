<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

final class Ledger
{
    public function __construct(
        private Account $account,
    ) {
    }

    public function balance(): int
    {
        if ($this->isDebitPositive()) {
            // TODO(zmd): implement me
            return 0;
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
