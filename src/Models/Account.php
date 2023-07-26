<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Enums\AccountType;

final class Account extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_accounts';

    protected $fillable = [
        'type',
        'number',
        'name',
    ];

    protected $casts = [
        'type' => AccountType::class,
    ];

    protected static function newFactory()
    {
        return AccountFactory::new();
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function ledger(): HasMany
    {
        return $this->lineItems()->ledger();
    }
}
