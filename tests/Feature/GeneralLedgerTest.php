<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;

it('can model a chart of accounts', function () {
    $accountAttributes = [
        ['1000',  'Assets',            AccountType::Asset],
        ['1100',  'Cash',              AccountType::Asset],
        ['1200',  'Equipment',         AccountType::Asset],
        ['2000',  'Liabilities',       AccountType::Liability],
        ['2100',  'Accounts Payable',  AccountType::Liability],
        ['3000',  'Equity',            AccountType::Equity],
        ['3100',  'Capital',           AccountType::Equity],
        ['4000',  'Income',            AccountType::Revenue],
        ['5000',  'Expenses',          AccountType::Expense],
    ];

    foreach ($accountAttributes as [$number, $name, $type]) {
        Account::create([
            'number' => $number,
            'type' => $type,
            'name' => $name,
        ]);
    }

    $accounts = Account::all();

    foreach ($accountAttributes as $index => [$number, $name, $type]) {
        expect($accounts[$index]->number)->toBe($number);
        expect($accounts[$index]->type)->toBe($type);
        expect($accounts[$index]->name)->toBe($name);
    }
});
