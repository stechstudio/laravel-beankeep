<?php

declare(strict_types=1);

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;

it('can model the chart of accounts', function () {
    $accountAttributes = [
        ['1000', AccountType::Asset, 'Assets'],
        ['2000', AccountType::Liability, 'Liabilities'],
        ['3000', AccountType::Equity, 'Equity'],
        ['4000', AccountType::Revenue, 'Income'],
        ['5000', AccountType::Expense, 'Expenses'],
    ];

    foreach ($accountAttributes as [$number, $type, $name]) {
        Account::create([
            'number' => $number,
            'type' => $type,
            'name' => $name,
        ]);
    }

    $accounts = Account::all();

    foreach ($accountAttributes as $index => [$number, $type, $name]) {
        expect($accounts[$index]->number)->toBe($number);
        expect($accounts[$index]->type)->toBe($type);
        expect($accounts[$index]->name)->toBe($name);
    }
});
