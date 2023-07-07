<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface KeepableAccount extends Keepable
{
    public function getKeepableType(): string;

    public function getKeepableName(): string;

    public function getKeepableNumber(): string;
}
