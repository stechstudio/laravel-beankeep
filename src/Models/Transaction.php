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

    public function lineItemsValid(): bool
    {
        $debitTotal = $this->lineItems()->sum('debit');
        $creditTotal = $this->lineItems()->sum('credit');

        return $debitTotal === $creditTotal;
    }
}
