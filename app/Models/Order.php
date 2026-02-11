<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'comment',
        'status',
        'promo_code_id',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    public function promoCode(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    public function getSubtotalAmountAttribute(): float
    {
        return $this->items->sum(fn (OrderItem $item) => (float) $item->price_amount * $item->quantity);
    }

    public function getTotalAmountAttribute(): string
    {
        $total = $this->getSubtotalAmountAttribute() - (float) ($this->discount_amount ?? 0);
        return number_format(max(0, $total), 0, ',', ' ') . ' ₽';
    }

    public static function statusOptions(): array
    {
        return [
            'new' => 'Новая',
            'confirmed' => 'Подтверждена',
            'in_progress' => 'В работе',
            'shipped' => 'Отправлена',
            'completed' => 'Выполнена',
            'cancelled' => 'Отменена',
        ];
    }
}
