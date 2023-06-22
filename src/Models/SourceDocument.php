<?php

declare(strict_types=1);

namespace STS\Beankeep\Laravel\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\TemporaryUploadedFile;

class SourceDocument extends Model
{
    protected $table = 'beankeep_source_documents';

    protected $fillable = [
        'date',
        'memo',
        'attachment',
        'filename',
        'mime_type',
    ];

    public static function generateAttachmentName(): string
    {
        return Str::uuid()->toString();
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(static::diskName());
    }

    public static function diskName(): string
    {
        return 'documents';
    }

    // TODO(zmd): reconsider making Livewire temp file a hard dependency here
    //   (can we code to a contract instead?)
    public function store(TemporaryUploadedFile $tempUploadFile): self
    {
        $this->attachment = static::generateAttachmentName();
        $this->filename = $tempUploadFile->getClientOriginalName();
        $this->mime_type = MimeDetector::fromStream(
            $tempUploadFile->readStream(),
        );

        $tempUploadFile->storeAs(
            $this->attachmentDirectory(),
            $this->attachment,
            static::diskName(),
        );

        return $this;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function temporaryUrl(): string
    {
        return static::disk()->temporaryUrl(
            $this->attachmentPath(),
            now()->addMinutes(5),
            [
                'ResponseContentType' => $this->temporaryUrlMime(),
            ],
        );
    }

    public function isPreviewable(): bool
    {
        return $this->temporaryUrlMime() !== 'application/octet-stream';
    }

    protected function temporaryUrlMime(): string
    {
        return match ($this->mime_type) {
            'application/pdf' => $this->mime_type,
            'image/gif'       => $this->mime_type,
            'image/png'       => $this->mime_type,
            'image/jpeg'      => $this->mime_type,
            'image/webp'      => $this->mime_type,
            default           => 'application/octet-stream',
        };
    }

    public function attachmentPath(): string
    {
        return $this->attachmentDirectory() . "/{$this->attachment}";
    }

    protected function attachmentDirectory(): string
    {
        // TODO(zmd): we are probably going to need to think harder about this,
        //   to ensure files from different domains aren't mixed-- something
        //   the user should be able to configure and set up somehow.
        return "beankeep/source-documents";
    }
}
