<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

use STS\Beankeep\Models\Transaction;

interface KeepableSourceDocument extends Keepable
{
    public function getKeepableTransaction(): Transaction;

    public function getKeepableMemo(): ?string;

    public function getKeepableAttachment(): string;

    public function getKeepableFilename(): string;

    public function getKeepableMimeType(): string;
}
