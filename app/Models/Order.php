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
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    public function getTotalAmountAttribute(): string
    {
        $total = $this->items->sum(fn (OrderItem $item) => (float) $item->price_amount * $item->quantity);
        return number_format($total, 0, ',', ' ') . ' ₽';
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
