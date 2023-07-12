<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Account;

class SimpleAccountsReceivable extends Model
{
    protected $table = 'accounts_receivable';

    protected $fillable = [
        'number',
        'name',
    ];

    public function keeper(): MorphOne
    {
        return $this->morphOne(Account::class, 'keepable');
    }
}
