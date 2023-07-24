<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

final class Transaction extends Beankeeper
{
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
        static::creating(function (Transaction $transaction) {
            return $transaction->posted === false;
        });

        static::updating(function (Transaction $transaction) {
            return $transaction->lineItemsValid();
        });
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function sourceDocuments(): HasMany
    {
        return $this->hasMany(SourceDocument::class);
    }

    public function post(): bool
    {
        if (!$this->lineItemsValid()) {
            return false;
        }

        $this->posted = true;

        return $this->save();
    }

    private function lineItemsValid(): bool
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
