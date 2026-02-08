<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'cover_media_id',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function coverMedia()
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_id');
    }

    /**
     * Фото/медиа в альбоме (через pivot album_items с sort_order).
     */
    public function items()
    {
        return $this->belongsToMany(MediaAsset::class, 'album_items', 'album_id', 'media_id')
            ->withPivot('sort_order')
            ->orderBy('album_items.sort_order');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
