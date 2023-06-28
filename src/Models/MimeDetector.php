<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use TypeError;

class MimeDetector
{
    /**
     * @param resource $fileStream
     */
    public static function fromStream($fileStream): ?string
    {
        if (!is_resource($fileStream)) {
            throw new \TypeError('$fileStream must be a resource');
        }

        return static::fromContents(stream_get_contents($fileStream));
    }

    public static function fromContents(string $fileContents): ?string
    {
        return (new FinfoMimeTypeDetector())
            ->detectMimeType('', $fileContents);
    }

    public static function fromPath(string|Filesystem $disk, $pathname): ?string
    {
        if (is_string($disk)) {
            $disk = Storage::disk($disk);
        }

        $stream = $disk->readStream($pathname);

        if (!$stream) {
            return null;
        }

        return static::fromStream($stream);
    }

    public static function fromExtension(string $pathname): ?string
    {
        return (new FinfoMimeTypeDetector())
            ->detectMimeTypeFromPath($pathname);
    }
}
