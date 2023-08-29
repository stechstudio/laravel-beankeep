<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use STS\Beankeep\Database\Factories\JournalFactory;
use STS\Beankeep\Enums\JournalPeriod;

final class Journal extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_journals';

    protected $fillable = [
        'period',
    ];

    protected $casts = [
        'period' => JournalPeriod::class,
    ];

    protected static function newFactory()
    {
        return JournalFactory::new();
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
