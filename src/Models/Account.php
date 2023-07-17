<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use STS\Beankeep\Enums\AccountType;

final class Account extends Beankeeper
{
    protected $table = 'beankeep_accounts';

    protected $fillable = [
        'type',
        'number',
        'name',
    ];

    protected $casts = [
        'type' => AccountType::class,
    ];

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }
}
