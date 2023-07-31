<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

final class Transactor
{
    private ?string $memo = null;

    private Transaction $transaction;

    private array $lineItems = [];

    private array $sourceDocuments = [];

    private AccountLookup $accounts;

    public function __construct(
        private Carbon|CarbonImmutable|null $date = null,
    ) {
    }

    public function transact(
        string $memo,
        Carbon|CarbonImmutable|null $date = null,
    ): self {
        $this->memo($memo);

        if ($date) {
            return $this->date($date);
        }

        return $this;
    }

    public function on(Carbon|CarbonImmutable $date): self
    {
        return $this->date($date);
    }

    public function date(Carbon|CarbonImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function memo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function line(
        string $accountKey,
        float $dr = 0.0,
        float $cr = 0.0,
    ): self {
        $this->lineItems[] = LineItem::factory()->make([
            'account_id' => $this->getAccounts()[$accountKey]->id,
            'debit' => (int) ($dr * 100),
            'credit' => (int) ($cr * 100),
        ]);

        return $this;
    }

    public function doc(
        string $filename,
        ?string $mimeType = null,
        ?string $attachment = null,
    ): self {
        $this->sourceDocuments[] = SourceDocument::factory()
            ->make(array_filter([
                'memo' => $this->memo,
                'attachment' => $attachment,
                'filename' => $filename,
                'mime_type' => $mimeType,
            ]));

        return $this;
    }

    public function post(): Transaction
    {
        return $this->save(posted: true);
    }

    public function draft(): Transaction
    {
        return $this->save();
    }

    public function save(bool $posted = false): Transaction
    {
        $transaction = Transaction::factory()->create(array_filter([
            'date' => $this->date,
            'memo' => $this->memo,
        ]));

        foreach ($this->lineItems as $lineItem) {
            $transaction->lineItems()->save($lineItem);
        }

        foreach ($this->sourceDocuments as $sourceDocument) {
            $transaction->sourceDocuments()->save($sourceDocument);
        }

        if ($posted) {
            $transaction->posted = true;
            $transaction->save();
        }

        return $transaction;
    }

    private function getAccounts(): AccountLookup
    {
        return $this->accounts ??= new AccountLookup();
    }
}
