<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class PromoCode extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'type',
        'value',
        'valid_from',
        'valid_until',
        'usage_limit',
        'times_used',
        'min_order_amount',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promo_code_product');
    }

    /**
     * Проверка: промокод действует и применим к корзине.
     *
     * @param  array{items: array, total: float}  $cart
     * @return array{valid: bool, message?: string}
     */
    public function validateForCart(array $cart): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'Промокод недействителен.'];
        }

        $now = Carbon::now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return ['valid' => false, 'message' => 'Промокод ещё не действует.'];
        }
        if ($this->valid_until && $now->gt(Carbon::parse($this->valid_until->format('Y-m-d'))->endOfDay())) {
            return ['valid' => false, 'message' => 'Срок действия промокода истёк.'];
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Лимит использований промокода исчерпан.'];
        }

        $applicableTotal = $this->getApplicableTotal($cart);
        $minOrder = $this->min_order_amount !== null ? (float) $this->min_order_amount : 0;
        if ($minOrder > 0 && $applicableTotal < $minOrder - 0.01) {
            $min = number_format($minOrder, 0, ',', ' ');
            return ['valid' => false, 'message' => "Минимальная сумма заказа для промокода: {$min} ₽."];
        }

        if ($applicableTotal <= 0) {
            $productIds = $this->products()->pluck('id')->toArray();
            if (! empty($productIds)) {
                return ['valid' => false, 'message' => 'Промокод действует только на определённые товары. В вашей корзине их нет.'];
            }
            return ['valid' => false, 'message' => 'Добавьте товары в корзину для применения промокода.'];
        }

        return ['valid' => true];
    }

    /**
     * Сумма корзины, к которой применяется скидка (все товары или только из списка продуктов).
     */
    public function getApplicableTotal(array $cart): float
    {
        $productIds = $this->products()->pluck('id')->toArray();
        if (empty($productIds)) {
            return (float) ($cart['total'] ?? 0);
        }
        $total = 0.0;
        foreach ($cart['items'] ?? [] as $row) {
            if (in_array($row['product']->id, $productIds, true)) {
                $total += $row['subtotal'];
            }
        }
        return $total;
    }

    /**
     * Рассчитать размер скидки по корзине.
     */
    public function calculateDiscount(array $cart): float
    {
        $applicableTotal = $this->getApplicableTotal($cart);
        if ($applicableTotal <= 0) {
            return 0.0;
        }
        $value = (float) $this->value;
        if ($this->type === self::TYPE_PERCENT) {
            $discount = round($applicableTotal * $value / 100, 2);
        } else {
            $discount = min($value, $applicableTotal);
        }
        return round($discount, 2);
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_PERCENT => 'Процент',
            self::TYPE_FIXED => 'Фиксированная сумма',
        ];
    }
}
