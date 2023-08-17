<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use STS\Beankeep\Database\Factories\JournalFactory;

final class Journal extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_journals';

    protected $fillable = [
        'period',  // integer (JournalPeriod enum)
    ];

    // TODO(zmd): period -> JournalPeriod enum
    protected $casts = [
    ];

    protected static function newFactory()
    {
        return JournalFactory::new();
    }
}
