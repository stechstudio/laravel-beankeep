<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

class StaticSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAccountsIfNeeded();

        // TODO(zmd): implement me
    }

    protected function seedAccountsIfNeeded(): void
    {
        if (!Account::count()) {
            $this->call([AccountSeeder::class]);
        }
    }
}
