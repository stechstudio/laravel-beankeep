<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

interface Savable extends Keepable
{
    public function serializeToBeankeep(): array;
}
