<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models\Augmented;

use Illuminate\Database\Eloquent\Model;
use STS\Beankeep\Concerns\HasJournal;
use STS\Beankeep\Contracts\Keepable;

class Journal extends Model implements Keepable
{
    use HasJournal;

    protected $table = 'augmented_journals';

    protected $fillable = [
        'name',
    ];
}
