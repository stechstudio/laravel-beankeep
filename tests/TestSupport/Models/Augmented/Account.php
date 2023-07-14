<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models\Augmented;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Concerns\HasAccount;
use STS\Beankeep\Contracts\Keepable;

class Account extends Model implements Keepable
{
    use HasAccount;

    protected $table = 'augmented_accounts';

    protected $fillable = [
        'description',
    ];
}
