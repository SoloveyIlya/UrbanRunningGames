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
        'mime_type',
        'width',
        'height',
        'original_name',
    ];

    /**
     * URL для отображения (публичный диск).
     */
    public function getUrlAttribute(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }

    public function isImage(): bool
    {
        return ($this->type ?? 'image') === 'image';
    }
}
