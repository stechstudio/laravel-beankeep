<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use STS\Beankeep\Database\Factories\SourceDocumentFactory;

final class SourceDocument extends Beankeeper
{
    use HasFactory;

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

    protected static function newFactory()
    {
        return SourceDocumentFactory::new();
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
