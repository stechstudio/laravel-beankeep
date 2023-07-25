<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use STS\Beankeep\Database\Factories\TransactionFactory;

final class Transaction extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_transactions';

    protected $fillable = [
        'date',
        'memo',
    ];

    protected $casts = [
        'date' => 'date',
        'posted' => 'boolean',
    ];

    protected $attributes = [
        'posted' => false,
    ];

    protected static function booted(): void
    {
        static::saving(function (Transaction $transaction) {
            if ($transaction->posted) {
                return $transaction->canPost();
            }
        });
    }

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function sourceDocuments(): HasMany
    {
        return $this->hasMany(SourceDocument::class);
    }

    public function canPost(): bool
    {
        return $this->lineItemsPresent() && $this->lineItemsBalance();
    }

    private function lineItemsPresent(): bool
    {
        return $this->lineItems()->count() !== 0;
    }

    private function lineItemsBalance(): bool
    {
        $debitTotal = $this->lineItems()->sum('debit');
        $creditTotal = $this->lineItems()->sum('credit');

        return $debitTotal === $creditTotal;
    }
}
