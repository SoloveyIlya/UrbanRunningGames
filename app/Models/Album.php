<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Album extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_PHOTOS = 'photos';

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_PHOTOS)
            ->useDisk(config('media-library.disk_name', 'public'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->nonQueued();
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
     * Фото/медиа в альбоме (через pivot album_items).
     */
    public function items()
    {
        return $this->belongsToMany(MediaAsset::class, 'album_items', 'album_id', 'media_id')
            ->withPivot('sort_order')
            ->orderBy('album_items.sort_order');
    }

    /**
     * Список фото для галереи. Приоритет у Spatie Media (коллекция photos), иначе legacy items.
     * Элемент: ['url', 'thumb', 'is_image'].
     */
    public function getGalleryPhotos(): Collection
    {
        $media = $this->getMedia(self::MEDIA_COLLECTION_PHOTOS);
        if ($media->isNotEmpty()) {
            return $media->map(fn ($m) => [
                'url' => $m->getUrl(),
                'thumb' => $m->getUrl('thumb') ?: $m->getUrl(),
                'is_image' => true,
            ]);
        }
        return $this->items->map(fn (MediaAsset $item) => [
            'url' => $item->url,
            'thumb' => $item->thumbnail_url ?? $item->url,
            'is_image' => $item->isImage(),
        ]);
    }

    /**
     * URL обложки: первое фото Spatie (thumb) или legacy cover_media.
     */
    public function getCoverUrl(): ?string
    {
        $first = $this->getFirstMedia(self::MEDIA_COLLECTION_PHOTOS);
        if ($first) {
            return $first->getUrl('thumb') ?: $first->getUrl();
        }
        if ($this->coverMedia && $this->coverMedia->isImage()) {
            return $this->coverMedia->thumbnail_url ?? $this->coverMedia->url;
        }
        return null;
    }

    /**
     * Количество фото (Spatie + legacy).
     */
    public function getPhotosCountAttribute(): int
    {
        $legacyCount = array_key_exists('items_count', $this->getAttributes())
            ? (int) $this->items_count
            : $this->items()->count();

        // Prefer already-loaded media relation or eager-loaded counts to avoid N+1 queries.
        if ($this->relationLoaded('media')) {
            $spatieCount = $this->media
                ->where('collection_name', self::MEDIA_COLLECTION_PHOTOS)
                ->count();
        } elseif (array_key_exists('media_count', $this->getAttributes())) {
            // Use eager-loaded media_count if present (from withCount('media')).
            $spatieCount = (int) $this->media_count;
        } else {
            // Fallback: efficient count query filtered by collection_name.
            $spatieCount = $this->media()
                ->where('collection_name', self::MEDIA_COLLECTION_PHOTOS)
                ->count();
        }

        return $spatieCount + $legacyCount;
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
