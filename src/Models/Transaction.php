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

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function sourceDocuments(): HasMany
    {
        return $this->hasMany(SourceDocument::class);
    }
}
