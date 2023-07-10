<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Contracts\Keepable;
use STS\Beankeep\Models\SourceDocument;

trait KeepAsSourceDocument
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(SourceDocument::class, 'keepable');
    }

    public function keepAttributes(): array
    {
        return [
            'transaction_id' => $this->getKeepableTransaction()->id;
            'memo' => $this->getKeepableMemo(),
            'attachment' => $this->getKeepableAttachment(),
            'filename' => $this->getKeepableFilename(),
            'mime_type' => $this->getKeepableMimeType(),
        ];
    }

    public static function bootKeepAsSourceDocument(): void
    {
        self::created(function (Keepable $model) {
            $sourceDocument = new SourceDocument($model->keepAttributes());
            $sourceDocument->keepable()->associate($model);

            $sourceDocument->save();
        });
    }
}
