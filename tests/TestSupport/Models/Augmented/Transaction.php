<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models\Augmented;

use Illuminate\Database\Eloquent\Model;
use STS\Beankeep\Concerns\HasTransaction;
use STS\Beankeep\Contracts\Keepable;

class Transaction extends Model implements Keepable
{
    use HasTransaction;

    protected $table = 'augmented_transactions';

    protected $fillable = [
        'flag_for_review',
    ];

    protected $casts = [
        'flag_for_review' => 'boolean',
    ];
}
