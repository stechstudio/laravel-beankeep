<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

interface KeepableLineItem extends Keepable
{
    public function getKeepableDebit(): int;

    public function getKeepableCredit(): int;
}
