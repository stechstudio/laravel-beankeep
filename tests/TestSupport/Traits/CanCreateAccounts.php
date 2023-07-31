<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use STS\Beankeep\Database\Factories\Support\AccountLookup;
use STS\Beankeep\Database\Seeders\AccountSeeder;
use STS\Beankeep\Models\Account;

trait CanCreateAccounts
{
    protected function createAccounts(): array
    {
        foreach (AccountSeeder::accountsAttributes() as $attributes) {
            Account::create([
                'number' => $attributes['number'],
                'type' => $attributes['type'],
                'name' => $attributes['name'],
            ]);
        }

        return AccountLookup::lookupTable();
    }
}
