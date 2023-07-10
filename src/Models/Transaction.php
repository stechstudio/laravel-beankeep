<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'beankeep_transactions';

    protected $fillable = [
        'memo',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
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

    public function formattedDate(): string
    {
        return $this->date->format('m/d/Y');
    }
}
