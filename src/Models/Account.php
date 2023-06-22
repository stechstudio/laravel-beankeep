<?php

declare(strict_types=1);

namespace STS\Beankeep\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $table = 'beankeep_accounts';

    protected $fillable = [
        'type',
        'name',
        'number',
    ];

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }
}
