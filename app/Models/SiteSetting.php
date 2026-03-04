<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = Cache::remember('site_settings', 300, function () {
            return static::query()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        Cache::forget('site_settings');
    }

    /** Ключи для контактов и ссылок команды SPRUT */
    public const KEY_VK_URL = 'contact_vk_url';
    public const KEY_TELEGRAM_URL = 'contact_telegram_url';
    public const KEY_RUTUBE_URL = 'contact_rutube_url';
    public const KEY_EMAIL = 'contact_email';
    public const KEY_PHONE = 'contact_phone';
    public const KEY_SCHEDULE_WEEKDAYS = 'contact_schedule_weekdays';
    public const KEY_SCHEDULE_EVENTS = 'contact_schedule_events';
    public const KEY_SCHEDULE_NOTE = 'contact_schedule_note';
    public const KEY_COMPANY_NAME = 'contact_company_name';
    public const KEY_INN = 'contact_inn';
    public const KEY_KPP = 'contact_kpp';
    public const KEY_OGRN = 'contact_ogrn';

    /** Hero страницы магазина: затемнение оверлея (0–1) и media_id фонов слайдов */
    public const KEY_SHOP_HERO_OVERLAY_OPACITY = 'shop_hero_overlay_opacity';
    public const KEY_SHOP_HERO_SLIDE_1_MEDIA_ID = 'shop_hero_slide_1_media_id';
    public const KEY_SHOP_HERO_SLIDE_2_MEDIA_ID = 'shop_hero_slide_2_media_id';
    public const KEY_SHOP_HERO_SLIDE_3_MEDIA_ID = 'shop_hero_slide_3_media_id';

    /** @deprecated используйте KEY_SHOP_HERO_SLIDE_*_MEDIA_ID */
    public const KEY_SHOP_HERO_SLIDE_1 = 'shop_hero_slide_1';
    public const KEY_SHOP_HERO_SLIDE_2 = 'shop_hero_slide_2';
    public const KEY_SHOP_HERO_SLIDE_3 = 'shop_hero_slide_3';

    /** Страница «О команде» (/about): hero, миссия, участники (JSON) */
    public const KEY_ABOUT_HERO_TITLE = 'about_hero_title';
    public const KEY_ABOUT_HERO_SUBTITLE = 'about_hero_subtitle';
    public const KEY_ABOUT_MISSION_TITLE = 'about_mission_title';
    public const KEY_ABOUT_MISSION_CONTENT = 'about_mission_content';
    public const KEY_ABOUT_TEAM_MEMBERS = 'about_team_members';
    /** Hero страницы «О команде»: оверлей и фоновое изображение */
    public const KEY_ABOUT_HERO_OVERLAY_OPACITY = 'about_hero_overlay_opacity';
    public const KEY_ABOUT_HERO_BACKGROUND_MEDIA_ID = 'about_hero_background_media_id';

    /** Блок статистики на главной (4 карточки) */
    public static function homeStatKeys(): array
    {
        $keys = [];
        for ($i = 1; $i <= 4; $i++) {
            $keys[] = "home_stat_{$i}_number";
            $keys[] = "home_stat_{$i}_label";
            $keys[] = "home_stat_{$i}_desc";
        }
        return $keys;
    }

    public static function contactKeys(): array
    {
        return [
            self::KEY_VK_URL,
            self::KEY_TELEGRAM_URL,
            self::KEY_RUTUBE_URL,
            self::KEY_EMAIL,
            self::KEY_PHONE,
            self::KEY_SCHEDULE_WEEKDAYS,
            self::KEY_SCHEDULE_EVENTS,
            self::KEY_SCHEDULE_NOTE,
            self::KEY_COMPANY_NAME,
            self::KEY_INN,
            self::KEY_KPP,
            self::KEY_OGRN,
        ];
    }
}
