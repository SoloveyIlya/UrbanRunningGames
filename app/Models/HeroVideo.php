<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroVideo extends Model
{
    public const PAGE_MAIN = 'main';
    public const PAGE_EVENTS = 'events';

    protected $fillable = [
        'page',
        'video_media_id',
        'poster_media_id',
        'title',
        'button_text',
        'button_url',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function videoMedia()
    {
        return $this->belongsTo(MediaAsset::class, 'video_media_id');
    }

    public function posterMedia()
    {
        return $this->belongsTo(MediaAsset::class, 'poster_media_id');
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->videoMedia?->url;
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->posterMedia?->thumbnail_url ?: $this->posterMedia?->url;
    }

    public static function pageOptions(): array
    {
        return [
            self::PAGE_MAIN => 'Главная',
            self::PAGE_EVENTS => 'Страница «События»',
        ];
    }

    public function getPageLabelAttribute(): string
    {
        return self::pageOptions()[$this->page] ?? $this->page;
    }
}
