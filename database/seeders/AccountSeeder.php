<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Models\Account;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Account::factory()
            ->createMany(AccountFactory::defaultAccountAttributes());
    }
}
