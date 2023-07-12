<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Tests\TestCase;

final class GeneralLedgerTest extends TestCase
{
    public function testItCanModelAChartOfAccounts(): void
    {
        $accounts = array_values($this->createAccounts());

        foreach ($this->accountAttributes() as $index => [$number, $name, $type]) {
            $this->assertEquals($number, $accounts[$index]->number);
            $this->assertEquals($type, $accounts[$index]->type);
            $this->assertEquals($name, $accounts[$index]->name);
        }
    }

    public function testItCanRecordATransactionToTheJournal(): void
    {
        $accounts = $this->createAccounts();

        $transaction = $this->transaction(
            'initial owner contribution',
            '2022-01-01',
            posted: true,
        );

        $debit = $this->debit($accounts['cash'], $transaction, 1000000);
        $credit = $this->credit($accounts['capital'], $transaction, 1000000);
        $sourceDoc = $this->doc($transaction, 'contribution-moa.pdf');

        $transaction->refresh();

        $this->assertEquals('initial owner contribution', $transaction->memo);
        $this->assertEquals($this->date('2022-01-01'), $transaction->date);
        $this->assertTrue($transaction->posted);

        $this->assertEquals(2, $transaction->lineItems()->count());
        $this->assertEquals(1000000, $transaction->lineItems[0]->debit);
        $this->assertEquals($accounts['cash'], $transaction->lineItems[0]->account);
        $this->assertEquals(1000000, $transaction->lineItems[1]->credit);
        $this->assertEquals($accounts['capital'], $transaction->lineItems[1]->account);

        $this->assertEquals(1, $transaction->sourceDocuments()->count());
        $this->assertEquals(
            $sourceDoc->attachment,
            $transaction->sourceDocuments->first()->attachment,
        );
        $this->assertEquals(
            $sourceDoc->filename,
            $transaction->sourceDocuments->first()->filename,
        );
        $this->assertEquals(
            $sourceDoc->mime_type,
            $transaction->sourceDocuments->first()->mime_type,
        );
    }

    public function testItCanModelAJournalWithManyTransactions(): void
    {
        $transact = $this->simpleTransactor();

        //
        //      Date | Account            |        Dr |        Cr | Memo
        // ==========+====================+===========+===========+======================
        //  01/01/22 | Cash               |  10000.00 |           | initial owner con...
        //           |   Capital          |           |  10000.00 |
        // ----------+--------------------+-----------+-----------+----------------------
        //  10/15/22 | Equipment          |   5000.00 |           | 2 computers from ...
        //           |   Accounts Payable |           |   5000.00 |
        // ----------+--------------------+-----------+-----------+----------------------
        //  10/16/22 | Accounts Payable   |   5000.00 |           | ck no. 1337
        //           |   Cash             |           |   5000.00 |
        // ==========+====================+===========+===========+======================
        //           | TOTAL (Dr)         |  20000.00 |           |
        //           |   TOTAL (Cr)       |           |  20000.00 |
        //
        $transact('2022-01-01', 'initial owner contribution', 10000.00, dr: 'cash', cr: 'capital');
        $transact('2022-10-15', '2 computers from computers-r-us', 5000.00, dr: 'equipment', cr: 'accounts-payable');
        $transact('2022-10-16', 'ck no. 1337', 5000.00, dr: 'accounts-payable', cr: 'cash');

        // NOTE(zmd): later we'll *also* check individual account balances here,
        //   once we have created helpers for doing such in the package.
        $this->assertEquals(0, LineItem::sum('debit') - LineItem::sum('credit'));
    }

    // ------------------------------------------------------------------------

    public function createAccounts(): array
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

    public function accountAttributes(): array
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

    public function transaction(
        string $memo,
        Carbon|string|null $date = null,
        bool $posted = true,
    ): Transaction {
        return Transaction::create([
            'date' => $this->date($date),
            'posted' => $posted,
            'memo' => $memo,
        ]);
    }

    public function date(Carbon|string|null $date = null): Carbon
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } elseif (is_null($date)) {
            $date = Carbon::now();
        }

        return $date;
    }

    public function debit(
        Account $account,
        Transaction $transaction,
        int $amount,
    ): LineItem {
        return $this->item($account, $transaction, debitAmount: $amount);
    }

    public function credit(
        Account $account,
        Transaction $transaction,
        int $amount,
    ): LineItem {
        return $this->item($account, $transaction, creditAmount: $amount);
    }

    public function item(
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

    public function doc(
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

    public function mime(string $filename): string
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

    public function simpleTransactor(?array $accounts = null): Closure
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

            $transaction = $this->transaction($memo, $date, posted: true);

            $this->debit($accounts[$dr], $transaction, $amount);
            $this->credit($accounts[$cr], $transaction, $amount);
            $this->doc($transaction, Str::kebab($memo) . '.pdf');

            return $transaction;
        };
    }
}
