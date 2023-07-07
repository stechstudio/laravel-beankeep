<?php

declare(strict_types=1);

namespace STS\Beankeep\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\SourceDocument;

trait KeepableSourceDocument
{
    public function sourceDocument(): MorphOne
    {
        return $this->morphOne(SourceDocument::class, 'keepable');
    }
}
