<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

trait BeanConstructors
{
    protected array $accounts;

    protected function account(string $accountIndex): Account
    {
        return $this->accounts()[$accountIndex];
    }

    protected function accounts(): array
    {
        return $this->accounts ??= $this->createAccounts();
    }

    protected function createAccounts(): array
    {
        foreach ($this->accountAttributes() as [$number, $name, $type]) {
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

    protected function accountAttributes(): array
    {
        return [
            ['1000',  'Assets',              AccountType::Asset],
            ['1100',  'Cash',                AccountType::Asset],
            ['1200',  'Accounts Receivable', AccountType::Asset],
            ['1300',  'Equipment',           AccountType::Asset],
            ['2000',  'Liabilities',         AccountType::Liability],
            ['2100',  'Accounts Payable',    AccountType::Liability],
            ['3000',  'Equity',              AccountType::Equity],
            ['3100',  'Capital',             AccountType::Equity],
            ['4000',  'Revenue',             AccountType::Revenue],
            ['4100',  'Sales Income',        AccountType::Revenue],
            ['4200',  'Consulting Income',   AccountType::Revenue],
            ['5000',  'Expenses',            AccountType::Expense],
        ];
    }

    protected function transaction(
        string $memo,
        Carbon|string|null $date = null,
    ): Transaction {
        return Transaction::create([
            'date' => $this->date($date),
            'memo' => $memo,
        ]);
    }

    protected function date(Carbon|string|null $date = null): Carbon
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } elseif (is_null($date)) {
            $date = Carbon::now();
        }

        return $date;
    }

    protected function debit(
        Account $account,
        Transaction $transaction,
        int $amount,
    ): LineItem {
        return $this->item($account, $transaction, debitAmount: $amount);
    }

    protected function credit(
        Account $account,
        Transaction $transaction,
        int $amount,
    ): LineItem {
        return $this->item($account, $transaction, creditAmount: $amount);
    }

    protected function item(
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

    protected function doc(
        Transaction $transaction,
        string $filename,
        ?string $memo = null,
        ?string $mimeType = null,
        ?string $attachment = null,
    ): SourceDocument {
        if (is_null($mimeType)) {
            $mimeType = $this->mime($filename);
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

    protected function mime(string $filename): string
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

    protected function simpleTransactor(?array $accounts = null): Closure
    {
        if (is_null($accounts)) {
            $accounts = $this->createAccounts();
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

            $transaction = $this->transaction($memo, $date);

            $this->debit($accounts[$dr], $transaction, $amount);
            $this->credit($accounts[$cr], $transaction, $amount);
            $this->doc($transaction, Str::kebab($memo) . '.pdf');

            return $transaction;
        };
    }
}
