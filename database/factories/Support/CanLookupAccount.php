<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use STS\Beankeep\Models\Account;

trait CanLookupAccount
{
    protected AccountLookup $accountLookup;

    protected function account(string $accountKey): Account
    {
        return $this->getAccounts()[$accountKey];
    }

    protected function accounts(
        ?string $accountKey = null,
    ): AccountLookup|Account {
        return $accountKey
            ? $this->getAccounts()[$accountKey]
            : $this->getAccounts();
    }

    protected function refreshAccounts(): void
    {
        $this->getAccounts()->refresh();
    }

    protected function getAccounts(): AccountLookup
    {
        return $this->accountLookup ??= new AccountLookup();
    }
}
