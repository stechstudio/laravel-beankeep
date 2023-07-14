<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\SourceDocument;

trait HasSourceDocument
{
    public function getBeankeeperClass(): string
    {
        return SourceDocument::class;
    }

    public function keeper(): MorphOne
    {
        return $this->morphOne($this->getBeankeeperClass(), 'keepable');
    }
}
