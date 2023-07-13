<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use STS\Beankeep\Models\Beankept;

interface Savable extends Keepable
{
    public function convertToBeankeep(): Beankept;
}
