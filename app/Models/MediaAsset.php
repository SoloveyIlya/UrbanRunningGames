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
     * URL для отображения (относительный путь — всегда тот же хост, что и страница).
     */
    public function getUrlAttribute(): ?string
    {
        if (empty($this->path)) {
            return null;
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

    public function isImage(): bool
    {
        return ($this->type ?? 'image') === 'image';
    }
}
