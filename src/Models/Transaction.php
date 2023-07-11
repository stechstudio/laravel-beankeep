<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'beankeep_transactions';

    protected $fillable = [
        'date',
        'posted',
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

    public function keepable(): MorphTo
    {
        return $this->morphTo();
    }
}
