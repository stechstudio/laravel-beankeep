<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Illuminate\Database\Seeder;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Account::factory()->createMany($this->accountsAttributes());
        Transaction::factory()->count(100)->create();
    }

    protected function accountsAttributes(): array
    {
        return [
            ['number' => '1000', 'name' => 'Assets',              'type' => AccountType::Asset],
            ['number' => '1100', 'name' => 'Cash',                'type' => AccountType::Asset],
            ['number' => '1200', 'name' => 'Accounts Receivable', 'type' => AccountType::Asset],
            ['number' => '1300', 'name' => 'Equipment',           'type' => AccountType::Asset],
            ['number' => '2000', 'name' => 'Liabilities',         'type' => AccountType::Liability],
            ['number' => '2100', 'name' => 'Accounts Payable',    'type' => AccountType::Liability],
            ['number' => '3000', 'name' => 'Equity',              'type' => AccountType::Equity],
            ['number' => '3100', 'name' => 'Capital',             'type' => AccountType::Equity],
            ['number' => '4000', 'name' => 'Revenue',             'type' => AccountType::Revenue],
            ['number' => '4100', 'name' => 'Sales Income',        'type' => AccountType::Revenue],
            ['number' => '4200', 'name' => 'Consulting Income',   'type' => AccountType::Revenue],
            ['number' => '5000', 'name' => 'Expenses',            'type' => AccountType::Expense],
        ];
    }
}
