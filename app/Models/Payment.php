<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'provider',
        'external_payment_id',
        'amount',
        'currency',
        'status',
        'pay_url',
        'payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'payment_id');
    }

    public function markPaid(): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_PENDING], true);
    }
}
