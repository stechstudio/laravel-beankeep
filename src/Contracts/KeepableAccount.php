<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use STS\Beankeep\Enums\AccountType;

interface KeepableAccount extends Keepable
{
    public function getKeepableType(): AccountType|string;

    public function getKeepableName(): string;

    public function getKeepableNumber(): string;
}
