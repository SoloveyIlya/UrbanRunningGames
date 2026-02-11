<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_amount',
        'currency',
        'is_active',
        'cover_media_id',
    ];

    protected $casts = [
        'price_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_id');
    }

    /**
     * Фото для карусели (галерея товара), отсортированные по sort_order.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'product_media', 'product_id', 'media_id')
            ->withPivot('sort_order')
            ->orderBy('product_media.sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    /** Все варианты (включая неактивные) — для админки. */
    public function adminVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * URL главного изображения: обложка или первое из галереи.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if ($this->coverMedia) {
            return $this->coverMedia->thumbnail_url ?? $this->coverMedia->url;
        }
        $first = $this->media->first();
        return $first ? ($first->thumbnail_url ?? $first->url) : null;
    }

    /**
     * Цена с учётом варианта (если один вариант — его price_override).
     */
    public function getDisplayPriceAttribute(): string
    {
        $amount = $this->price_amount;
        $variants = $this->variants;
        if ($variants->count() === 1 && $variants->first()->price_override !== null) {
            $amount = $variants->first()->price_override;
        }
        return number_format((float) $amount, 0, ',', ' ') . ' ₽';
    }

    /**
     * Есть ли у товара атрибуты (размер/цвет) для выбора.
     */
    public function hasAttributes(): bool
    {
        return $this->variants->isNotEmpty();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
