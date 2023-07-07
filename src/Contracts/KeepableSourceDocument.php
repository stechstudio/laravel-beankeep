<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Carbon\CarbonImmutable;

namespace STS\Beankeep\Contracts;

interface KeepableSourceDocument extends Keepable
{
    public function getKeepableDate(): string|Carbon|CarbonImmutable;

    public function getKeepableMemo(): string;

    public function getKeepableAttachment(): ?string;

    public function getKeepableFilename(): ?string;

    public function getKeepableMimeType(): ?string;
}
