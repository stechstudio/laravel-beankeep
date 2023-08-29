<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Journal;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Account::factory()
            ->for(Journal::factory())
            ->createMany(AccountFactory::defaultAccountAttributes());
    }
}
