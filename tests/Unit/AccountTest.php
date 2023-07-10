<?php

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;

function createAccounts() {
    Account::create([
        'number' => '1000',
        'type' => AccountType::Asset,
        'name' => 'Assets',
    ]);
    Account::create([
        'number' => '2000',
        'type' => AccountType::Liability,
        'name' => 'Liabilities',
    ]);
    Account::create([
        'number' => '3000',
        'type' => AccountType::Equity,
        'name' => 'Equity',
    ]);
    Account::create([
        'number' => '4000',
        'type' => AccountType::Revenue,
        'name' => 'Income',
    ]);
    Account::create([
        'number' => '5000',
        'type' => AccountType::Expense,
        'name' => 'Expenses',
    ]);
}

it('can model the chart of accounts', function () {
    createAccounts();

    $accounts = Account::all();

    expect($accounts[0]->number)->toBe('1000');
    expect($accounts[0]->type)->toEqual(AccountType::Asset);
    expect($accounts[0]->name)->toBe('Assets');

    expect($accounts[1]->number)->toBe('2000');
    expect($accounts[1]->type)->toEqual(AccountType::Liability);
    expect($accounts[1]->name)->toBe('Liabilities');

    expect($accounts[2]->number)->toBe('3000');
    expect($accounts[2]->type)->toEqual(AccountType::Equity);
    expect($accounts[2]->name)->toBe('Equity');

    expect($accounts[3]->number)->toBe('4000');
    expect($accounts[3]->type)->toEqual(AccountType::Revenue);
    expect($accounts[3]->name)->toBe('Income');

    expect($accounts[4]->number)->toBe('5000');
    expect($accounts[4]->type)->toEqual(AccountType::Expense);
    expect($accounts[4]->name)->toBe('Expenses');
});
