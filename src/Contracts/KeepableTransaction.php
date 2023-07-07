<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;

interface KeepableTransaction extends Keepable
{
    public function getKeepableDate(): string|Carbon|CarbonImmutable;
}
