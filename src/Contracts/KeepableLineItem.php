<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Transaction;

interface KeepableLineItem extends Keepable
{
    public function getKeepableAccount(): Account;

    public function getKeepableTransaction(): Transaction;

    public function getKeepableDebit(): int;

    public function getKeepableCredit(): int;
}
