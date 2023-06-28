<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineItem extends Model
{
    protected $table = 'beankeep_line_items';

    protected $fillable = [
        'account_id',
        'transaction_id',
        'debit',
        'credit',
    ];

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

    public function formattedDebit(): string
    {
        return $this->isDebit()
            ? static::formattedAmount($this->debit)
            : '';
    }

    public function formattedCredit(): string
    {
        return $this->isCredit()
            ? static::formattedAmount($this->credit)
            : '';
    }

    public static function formattedAmount(int $amount): string
    {
        $amountInDollars = $amount / 100;

        return number_format($amountInDollars, 2, '.', ',');
    }
}
