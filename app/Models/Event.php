<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'city_id',
        'location_text',
        'starts_at',
        'description',
        'rules',
        'status',
        'level',
        'cover_media_id',
        'hero_video_media_id',
        'hero_ornament_media_id',
        'hero_ornament_opacity',
        'distance',
        'locations_count',
        'time_limit',
        'teams_count',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = static::uniqueSlug(Str::slug($event->title), $event->id);
            }
        });
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'event_partners');
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function coverMedia()
    {
        return $this->belongsTo(\App\Models\MediaAsset::class, 'cover_media_id');
    }

    public function heroVideoMedia()
    {
        return $this->belongsTo(\App\Models\MediaAsset::class, 'hero_video_media_id');
    }

    public function heroOrnamentMedia()
    {
        return $this->belongsTo(\App\Models\MediaAsset::class, 'hero_ornament_media_id');
    }

    public function distances()
    {
        return $this->hasMany(EventDistance::class)->orderBy('sort_order');
    }

    /** Подпись уровня для текущей локали (из level_translations). */
    public function getLevelLabelAttribute(): ?string
    {
        if (!$this->level) {
            return null;
        }
        return LevelTranslation::labelFor($this->level);
    }

    /**
     * URL главной картинки события (для карточки на главной). Задаётся в админке.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if ($this->coverMedia) {
            return $this->coverMedia->thumbnail_url ?? $this->coverMedia->url;
        }
        return null;
    }

    /** URL видео для hero страницы гонки (если задано у гонки). */
    public function getHeroVideoUrlAttribute(): ?string
    {
        return $this->heroVideoMedia?->url;
    }

    /** URL орнамента для hero страницы гонки (если задано у гонки). */
    public function getHeroOrnamentUrlAttribute(): ?string
    {
        return $this->heroOrnamentMedia?->url;
    }

    /** Прозрачность орнамента hero (0–1), по умолчанию 0.85. */
    public function getHeroOrnamentOpacityAttribute($value): float
    {
        if ($value !== null && $value !== '') {
            return (float) $value;
        }
        return 0.85;
    }

    /**
     * Возвращает уникальный slug: при совпадении добавляет суффикс -2, -3 и т.д.
     */
    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $num = 1;
        $query = static::query()->where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        while ($query->exists()) {
            $slug = $base . '-' . (++$num);
            $query = static::query()->where('slug', $slug);
            if ($excludeId !== null) {
                $query->where('id', '!=', $excludeId);
            }
        }
        return $slug;
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at > now();
    }

    public function isPast(): bool
    {
        return $this->starts_at <= now();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'published' => $this->isUpcoming() ? 'Предстоящее' : 'Завершено',
            'closed' => 'Закрыто',
            'archived' => 'Архив',
            default => 'Черновик',
        };
    }
}
