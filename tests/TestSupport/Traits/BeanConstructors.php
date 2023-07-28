<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use STS\Beankeep\Database\Factories\SourceDocumentFactory;
use STS\Beankeep\Database\Factories\Support\AccountLookup;
use STS\Beankeep\Database\Seeders\AccountSeeder;
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
        foreach (AccountSeeder::accountsAttributes() as $attributes) {
            Account::create([
                'number' => $attributes['number'],
                'type' => $attributes['type'],
                'name' => $attributes['name'],
            ]);
        }

        return AccountLookup::lookupTable();
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
            $mimeType = SourceDocumentFactory::mime($filename);
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
