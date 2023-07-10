<?php

declare(strict_types=1);

namespace STS\Beankeep\Contracts;

interface KeepableSourceDocument extends Keepable
{
    public function getKeepablePosted(): boolean;

    public function getKeepableMemo(): string;

    public function getKeepableAttachment(): ?string;

    public function getKeepableFilename(): ?string;

    public function getKeepableMimeType(): ?string;
}
