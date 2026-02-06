<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'topic',
        'phone',
        'email',
        'message',
        'status',
        'ip',
        'user_agent',
    ];

    public function getTopicLabelAttribute(): string
    {
        return match($this->topic) {
            'participation' => 'Участие в забеге',
            'merch' => 'Мерч',
            'partnership' => 'Партнёрство',
            default => 'Другое',
        };
    }
}
