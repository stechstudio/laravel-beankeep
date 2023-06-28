<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use STS\Beankeep\Enums\AccountType;

class Account extends Model
{
    protected $table = 'beankeep_accounts';

    protected $fillable = [
        'type',
        'name',
        'number',
    ];

    protected $casts = [
        'type' => AccountType::class,
    ];


    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }
}
