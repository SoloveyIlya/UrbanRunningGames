<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price_amount',
    ];

    protected $casts = [
        'price_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getDisplayPriceAttribute(): string
    {
        return number_format((float) $this->price_amount, 0, ',', ' ') . ' ₽';
    }

    public function getSubtotalAttribute(): string
    {
        $sum = (float) $this->price_amount * $this->quantity;
        return number_format($sum, 0, ',', ' ') . ' ₽';
    }

    public function getVariantLabelAttribute(): string
    {
        return $this->productVariant?->attribute_label ?? '—';
    }
}
