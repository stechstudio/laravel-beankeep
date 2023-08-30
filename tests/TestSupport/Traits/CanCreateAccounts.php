<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use STS\Beankeep\Database\Factories\Support\AccountLookup;
use STS\Beankeep\Database\Factories\Support\CanLookupAccounts;
use STS\Beankeep\Database\Seeders\AccountSeeder;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Journal;

trait CanCreateAccounts
{
    use CanLookupAccounts;

    protected Journal $journal;

    protected function createAccounts(): array
    {
        $this->seed(AccountSeeder::class);

        $accounts = AccountLookup::lookupTable();

        $this->journal = $accounts['cash']->journal;

        return $accounts;
    }

    protected function createAccountsIfMissing(): void
    {
        if (!Account::count()) {
            $this->createAccounts();
        }
    }
}
