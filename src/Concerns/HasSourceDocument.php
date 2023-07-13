<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\SourceDocument;

trait HasSourceDocument
{
    public function kept(): MorphOne
    {
        return $this->morphOne(SourceDocument::class, 'keepable');
    }
}
