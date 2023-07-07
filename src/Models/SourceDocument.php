<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceDocument extends Model
{
    protected $table = 'beankeep_source_documents';

    protected $fillable = [
        'date',
        'memo',
        'attachment',
        'filename',
        'mime_type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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
