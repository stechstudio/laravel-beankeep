<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\SourceDocument;

trait KeepAsSourceDocument
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(SourceDocument::class, 'keepable');
    }
}
