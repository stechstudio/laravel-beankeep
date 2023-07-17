<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models\Augmented;

use Illuminate\Database\Eloquent\Model;
use STS\Beankeep\Concerns\HasSourceDocument;
use STS\Beankeep\Contracts\Keepable;

class SourceDocument extends Model implements Keepable
{
    use HasSourceDocument;

    protected $table = 'augmented_source_documents';

    protected $fillable = [
        //
    ];
}
