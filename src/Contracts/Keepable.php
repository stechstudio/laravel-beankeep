<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Keepable
{
    public function keeper(): MorphOne;
}
