<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Keepable
{
    public static function getBeankeeperClass(): string;

    public function keeper(): MorphOne;
}
