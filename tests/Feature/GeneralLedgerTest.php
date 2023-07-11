<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

it('can model a chart of accounts', function () {
    $accounts = array_values(createAccounts());

    foreach (accountAttributes() as $index => [$number, $name, $type]) {
        expect($accounts[$index]->number)->toBe($number);
        expect($accounts[$index]->type)->toBe($type);
        expect($accounts[$index]->name)->toBe($name);
    }
});

it('can record a transaction to the journal', function () {
    $accounts = createAccounts();

    $transaction = Transaction::create([
        'date' => Carbon::parse('2022-01-01'),
        'posted' => true,
        'memo' => 'initial owner contribution',
    ]);

    $debit = new LineItem(['debit' => 1000000, 'credit' => 0]);
    $debit->account()->associate($accounts['cash'])
        ->transaction()->associate($transaction)
        ->save();

    $credit = new LineItem(['debit' => 0, 'credit' => 1000000]);
    $credit->account()->associate($accounts['capital'])
        ->transaction()->associate($transaction)
        ->save();

    $sourceDoc = new SourceDocument([
        'attachment' => Str::uuid()->toString(),
        'filename' => 'contribution-moa.pdf',
        'mime_type' => 'appliction/pdf',
    ]);
    $sourceDoc->transaction()->associate($transaction)->save();

    $transaction->refresh();

    expect($transaction->date)->toEqual(Carbon::parse('2022-01-01'));
    expect($transaction->posted)->toBeTrue();
    expect($transaction->memo)->toBe('initial owner contribution');

    expect($transaction->lineItems()->count())->toBe(2);
    expect($transaction->lineItems[0]->debit)->toBe(1000000);
    expect($transaction->lineItems[0]->account)->toEqual($accounts['cash']);
    expect($transaction->lineItems[1]->credit)->toBe(1000000);
    expect($transaction->lineItems[1]->account)->toEqual($accounts['capital']);

    expect($transaction->sourceDocuments()->count())->toBe(1);
    expect($transaction->sourceDocuments->first()->attachment)
        ->toBe($sourceDoc->attachment);
    expect($transaction->sourceDocuments->first()->filename)
        ->toBe($sourceDoc->filename);
    expect($transaction->sourceDocuments->first()->mime_type)
        ->toBe($sourceDoc->mime_type);
});

function createAccounts(): array
{
    foreach (accountAttributes() as [$number, $name, $type]) {
        Account::create([
            'number' => $number,
            'type' => $type,
            'name' => $name,
        ]);
    }

    return Account::all()
        ->mapWithKeys(fn (Account $account) =>
            [Str::kebab($account->name) => $account])
        ->all();
}

function accountAttributes(): array
{
    return [
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
}
