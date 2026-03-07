<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_user_id',
        'action',
        'entity_type',
        'entity_id',
        'old',
        'new',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old' => 'array',
            'new' => 'array',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function getEntityLabelAttribute(): string
    {
        $label = match ($this->entity_type) {
            'events' => 'Событие',
            'products' => 'Товар',
            'orders' => 'Заявка',
            'partners' => 'Партнёр',
            'albums' => 'Альбом',
            'promo_codes' => 'Промокод',
            'rating_entries' => 'Рейтинг',
            'cities' => 'Город',
            'site_pages' => 'Страница сайта',
            'contact_messages' => 'Обращение',
            default => $this->entity_type,
        };

        return "{$label} #{$this->entity_id}";
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Создание',
            'update' => 'Изменение',
            'delete' => 'Удаление',
            'status_change' => 'Смена статуса',
            'import' => 'Импорт',
            default => $this->action,
        };
    }
}
