<?php

declare(strict_types=1);

use Closure;
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

    $transaction = transaction(
        'initial owner contribution',
        '2022-01-01',
        posted: true,
    );

    $debit = debit($accounts['cash'], $transaction, 1000000);
    $credit = credit($accounts['capital'], $transaction, 1000000);
    $sourceDoc = doc($transaction, 'contribution-moa.pdf');

    $transaction->refresh();

    expect($transaction->memo)->toBe('initial owner contribution');
    expect($transaction->date)->toEqual(d('2022-01-01'));
    expect($transaction->posted)->toBeTrue();

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

it('can model a journal with many transactions', function () {
    $transact = simpleTransactor();
    $transact('2022-01-01', 'initial owner contribution', 10000.00, dr: 'cash', cr: 'capital');

    // TODO(zmd): finish adding transactions, then set expectations around
    //    account debits and credits all balancing correctly
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

function simpleTransactor(?array $accounts = null): Closure
{
    if (is_null($accounts)) {
        $accounts = createAccounts();
    }

    return function (
        string|Carbon $date,
        string $memo,
        int|float $amount = null,
        string $dr,
        string $cr,
    ) use ($accounts): Transaction {
        if (is_float($amount)) {
            $amount = (int) ($amount * 100);
        }

        $transaction = transaction($memo, $date, posted: true);

        debit($accounts[$dr], $transaction, $amount);
        credit($accounts[$cr], $transaction, $amount);
        doc($transaction, Str::kebab($memo) . '.pdf');

        return $transaction;
    };
}

function transaction(
    string $memo,
    Carbon|string|null $date = null,
    bool $posted = true,
): Transaction {
    return Transaction::create([
        'date' => d($date),
        'posted' => $posted,
        'memo' => $memo,
    ]);
}

function d(Carbon|string|null $date = null): Carbon
{
    if (is_string($date)) {
        $date = Carbon::parse($date);
    } elseif (is_null($date)) {
        $date = Carbon::now();
    }

    return $date;
}

function debit(
    Account $account,
    Transaction $transaction,
    int $amount,
): LineItem {
    return item($account, $transaction, debitAmount: $amount);
}

function credit(
    Account $account,
    Transaction $transaction,
    int $amount,
): LineItem {
    return item($account, $transaction, creditAmount: $amount);
}

function item(
    Account $account,
    Transaction $transaction,
    int $debitAmount = 0,
    int $creditAmount = 0,
): LineItem {
    $lineItem = new LineItem([
        'debit' => $debitAmount,
        'credit' => $creditAmount,
    ]);

    $lineItem->account()->associate($account)
        ->transaction()->associate($transaction)
        ->save();

    return $lineItem;
}

function doc(
    Transaction $transaction,
    string $filename,
    ?string $memo = null,
    ?string $mimeType = null,
    ?string $attachment = null,
): SourceDocument {
    if (is_null($mimeType)) {
        $mimeType = mime($filename);
    }

    if (is_null($attachment)) {
        $attachment = Str::uuid()->toString();
    }

    $sourceDoc = new SourceDocument([
        'attachment' => $attachment,
        'filename' => $filename,
        'mime_type' => $mimeType,
    ]);

    $sourceDoc->transaction()->associate($transaction)->save();

    return $sourceDoc;
}

function mime(string $filename): string
{
    $parts = explode('.', $filename);
    $extension = end($parts);

    return match($extension) {
        'bmp' => 'image/bmp',
        'csv' => 'text/csv',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'gif' => 'image/gif',
        'htm', 'html' => 'text/html',
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf',
        'rtf' => 'application/rtf',
        'svg' => 'image/svg+xml',
        'tif', 'tiff' => 'image/tiff',
        'txt' => 'text/plain',
        'webp' => 'image/webp',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml' => 'application/xml',
        default => 'application/octet-stream',
    };
}
