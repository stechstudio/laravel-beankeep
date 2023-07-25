<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use STS\Beankeep\Models\SourceDocument;

class SourceDocumentFactory extends Factory
{
    protected $model = SourceDocument::class;

    protected static array $extensions = [
        'bmp',
        'c',
        'cpp',
        'csv',
        'doc',
        'docx',
        'gif',
        'htm',
        'html',
        'jpeg',
        'jpg',
        'js',
        'lua',
        'pdf',
        'php',
        'pl',
        'png',
        'py',
        'rb',
        'rtf',
        'svg',
        'tif',
        'tiff',
        'txt',
        'webp',
        'xhtml',
        'xls',
        'xlsx',
        'xml',
    ];

    public function definition(): array
    {
        $memo = $this->memo();
        $filename = Str::kebab($memo) . '.' . $this->extension();

        return [
            'memo' => $memo,
            'attachment' => Str::uuid()->toString(),
            'filename' => $filename,
            'mime_type' => static::mime($filename),
        ];
    }

    protected function memo(): string
    {
        $numWords = $this->faker->numberBetween(3, 7);

        return implode(' ', $this->faker->words($numWords));
    }

    public function extension(): string
    {
        return $this->faker->randomElement(static::$extensions);
    }

    public static function mime(string $filename): string
    {
        $parts = explode('.', $filename);
        $extension = end($parts);

        return match($extension) {
            'bmp' => 'image/bmp',
            'csv' => 'text/csv',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'gif' => 'image/gif',
            'htm', 'html' => 'text/html',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'rtf' => 'application/rtf',
            'svg' => 'image/svg+xml',
            'tif', 'tiff' => 'image/tiff',
            'txt' => 'text/plain',
            'webp' => 'image/webp',
            'xhtml' => 'application/xhtml+xml',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xml' => 'application/xml',
            default => 'application/octet-stream',
        };
    }

}
