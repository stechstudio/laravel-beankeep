<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Database\Seeders\AccountSeeder;
use STS\Beankeep\Database\Factories\Support\AccountLookup;
use STS\Beankeep\Database\Factories\Support\CanLookupAccounts;
use STS\Beankeep\Models\Account;

trait CanCreateAccounts
{
    use CanLookupAccounts;

    protected function createAccounts(): array
    {
        $this->seed(AccountSeeder::class);

        return AccountLookup::lookupTable();
    }
}
