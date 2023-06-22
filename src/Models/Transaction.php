<?php

declare(strict_types=1);

namespace STS\Beankeep\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'beankeep_transactions';

    protected $fillable = [
        'source_document_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function sourceDocument(): BelongsTo
    {
        return $this->belongsTo(SourceDocument::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function formattedDate(): string
    {
        return $this->date->format('m/d/Y');
    }
}
