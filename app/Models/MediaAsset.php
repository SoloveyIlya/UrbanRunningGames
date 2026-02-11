<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    protected $table = 'media_assets';

    protected $fillable = [
        'type',
        'disk',
        'path',
        'thumbnail_path',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'original_name',
        'created_by_admin_id',
    ];

    /**
     * URL для отображения. Если файла нет на диске — возвращается плейсхолдер (без 404).
     */
    public function getUrlAttribute(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        $disk = $this->disk ?: 'public';
        if (! Storage::disk($disk)->exists($this->path)) {
            return self::placeholderDataUri();
        }
        return '/storage/' . ltrim($this->path, '/');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (empty($this->thumbnail_path)) {
            return $this->url;
        }

        $disk = $this->disk ?: 'public';
        if (! Storage::disk($disk)->exists($this->thumbnail_path)) {
            return $this->url;
        }
        return '/storage/' . ltrim($this->thumbnail_path, '/');
    }

    public static function placeholderDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">'
            . '<rect fill="#eee" width="400" height="400"/>'
            . '<text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="14" text-anchor="middle" dy=".3em">Фото недоступно</text></svg>';

        return 'data:image/svg+xml,' . rawurlencode($svg);
    }

    public function isImage(): bool
    {
        return ($this->type ?? 'image') === 'image';
    }
}
