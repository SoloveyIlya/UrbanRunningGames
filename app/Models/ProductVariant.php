<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'gender',
        'sku',
        'price_override',
        'is_active',
    ];

    public static function getGenderLabels(): array
    {
        return [
            'M' => 'М',
            'Ж' => 'Ж',
        ];
    }

    public function getGenderLabelAttribute(): string
    {
        if ($this->gender === null) {
            return 'Универсальный';
        }

        return self::getGenderLabels()[$this->gender] ?? (string) $this->gender;
    }

    protected $casts = [
        'price_override' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Цена: переопределённая или базовая у товара.
     */
    public function getDisplayPriceAttribute(): string
    {
        $amount = $this->price_override ?? $this->product->price_amount;
        return number_format((float) $amount, 0, ',', ' ') . ' ₽';
    }

    /**
     * Краткое описание варианта (размер).
     */
    public function getAttributeLabelAttribute(): string
    {
        return $this->size !== null && $this->size !== '' ? $this->size : '—';
    }
}
