<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models\Augmented;

use Illuminate\Database\Eloquent\Model;
use STS\Beankeep\Concerns\HasLineItem;
use STS\Beankeep\Contracts\Keepable;

class LineItem extends Model implements Keepable
{
    use HasLineItem;

    protected $table = 'augmented_line_items';

    protected $fillable = [
        'narration'
    ];
}
