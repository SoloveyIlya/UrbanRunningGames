<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    /**
     * Записать действие в журнал (только при запросе из админки).
     */
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $old = null,
        ?array $new = null,
    ): ?AuditLog {
        if (! static::shouldLog()) {
            return null;
        }

        $adminId = auth()->id();

        return AuditLog::create([
            'admin_user_id' => $adminId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old' => $old,
            'new' => $new,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected static function shouldLog(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        // Прямой запрос к админке или Livewire-запрос с админ-страницы
        if (request()->is('admin*')) {
            return true;
        }
        $referer = request()->header('referer', '');
        return str_contains($referer, '/admin');
    }

    /**
     * Маппинг модели в тип сущности для лога.
     */
    public static function entityTypeFromModel(object $model): string
    {
        $map = [
            \App\Models\Event::class => 'events',
            \App\Models\Product::class => 'products',
            \App\Models\Order::class => 'orders',
            \App\Models\Partner::class => 'partners',
            \App\Models\Album::class => 'albums',
            \App\Models\PromoCode::class => 'promo_codes',
            \App\Models\RatingEntry::class => 'rating_entries',
            \App\Models\City::class => 'cities',
            \App\Models\SitePage::class => 'site_pages',
            \App\Models\ContactMessage::class => 'contact_messages',
            \App\Models\HeroVideo::class => 'hero_videos',
        ];

        return $map[get_class($model)] ?? class_basename($model);
    }
}
