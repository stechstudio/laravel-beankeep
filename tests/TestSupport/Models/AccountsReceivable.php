<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsReceivable extends Model
{
    protected $fillable = [
        'number',
    ];
}
