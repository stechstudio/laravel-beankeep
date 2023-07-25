<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use STS\Beankeep\Database\Factories\LineItemFactory;

final class LineItem extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_line_items';

    protected $fillable = [
        'account_id',
        'transaction_id',
        'debit',
        'credit',
    ];

    protected $attributes = [
        'debit' => 0,
        'credit' => 0,
    ];

    protected static function booted(): void
    {
        static::saving(function (LineItem $lineItem) {
            return $lineItem->isDebit() xor $lineItem->isCredit();
        });
    }

    protected static function newFactory()
    {
        return LineItemFactory::new();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function isDebit(): bool
    {
        return $this->debit > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit > 0;
    }

    public function debitInDollars(): float
    {
        return $this->debit / 100;
    }

    public function creditInDollars(): float
    {
        return $this->credit / 100;
    }
}
