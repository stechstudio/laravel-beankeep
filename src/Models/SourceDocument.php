<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourceDocument extends Beankept
{
    protected $table = 'beankeep_source_documents';

    protected $fillable = [
        'transaction_id',
        'memo',
        'attachment',
        'filename',
        'mime_type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function keepable(): MorphTo
    {
        return $this->morphTo();
    }
}
