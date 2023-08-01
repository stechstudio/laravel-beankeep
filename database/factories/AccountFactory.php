<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [];
    }

    public static function defaultAccountAttributes(): array
    {
        return [
            ['number' => '1000', 'name' => 'Assets',              'type' => AccountType::Asset],
                ['number' => '1100', 'name' => 'Cash',                'type' => AccountType::Asset],
                ['number' => '1200', 'name' => 'Accounts Receivable', 'type' => AccountType::Asset],
                ['number' => '1300', 'name' => 'Equipment',           'type' => AccountType::Asset],
                ['number' => '1400', 'name' => 'Prepaid Insurance',   'type' => AccountType::Asset],

            ['number' => '2000', 'name' => 'Liabilities',         'type' => AccountType::Liability],
                ['number' => '2100', 'name' => 'Accounts Payable',    'type' => AccountType::Liability],
                ['number' => '2200', 'name' => 'Interest Payable',    'type' => AccountType::Liability],

            ['number' => '3000', 'name' => 'Equity',              'type' => AccountType::Equity],
                ['number' => '3100', 'name' => 'Capital',             'type' => AccountType::Equity],
                ['number' => '3200', 'name' => 'Retained Earnings',   'type' => AccountType::Equity],

            ['number' => '4000', 'name' => 'Revenue',             'type' => AccountType::Revenue],
                ['number' => '4100', 'name' => 'Sales Revenue',        'type' => AccountType::Revenue],
                ['number' => '4200', 'name' => 'Services Revenue',        'type' => AccountType::Revenue],
                ['number' => '4300', 'name' => 'Consulting Revenue',   'type' => AccountType::Revenue],

            ['number' => '5000', 'name' => 'Expenses',            'type' => AccountType::Expense],
                ['number' => '5100', 'name' => 'Interest Expense',    'type' => AccountType::Expense],
        ];
    }
}
