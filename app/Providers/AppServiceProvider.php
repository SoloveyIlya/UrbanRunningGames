<?php

namespace App\Providers;

use App\Models\Album;
use App\Models\City;
use App\Models\Event;
use App\Models\Order;
use App\Models\Partner;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\RatingEntry;
use App\Models\SitePage;
use App\Models\HeroVideo;
use App\Models\SiteSetting;
use App\Observers\AuditableObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $observer = new AuditableObserver;
        Event::observe($observer);
        Product::observe($observer);
        Order::observe($observer);
        Partner::observe($observer);
        Album::observe($observer);
        PromoCode::observe($observer);
        RatingEntry::observe($observer);
        City::observe($observer);
        SitePage::observe($observer);
        HeroVideo::observe($observer);

        $siteContact = [
            'vk_url' => 'https://vk.com/urbanrunninggames',
            'telegram_url' => 'https://t.me/urbanrunninggames',
            'rutube_url' => '#',
            'email' => 'main@sprut.run',
            'phone' => '+79178060995',
            'schedule_weekdays' => 'Понедельник–пятница — 9:00–18:00',
            'schedule_events' => 'В дни мероприятий — 6:00–0:00',
            'schedule_note' => 'Отвечаем в Telegram',
            'company_name' => 'ООО «СПРУТ»',
            'inn' => '9731015256',
            'kpp' => '773101001',
            'ogrn' => '1187746928588',
        ];
        if (Schema::hasTable('site_settings')) {
            $siteContact = [
                'vk_url' => SiteSetting::get(SiteSetting::KEY_VK_URL, $siteContact['vk_url']),
                'telegram_url' => SiteSetting::get(SiteSetting::KEY_TELEGRAM_URL, $siteContact['telegram_url']),
                'rutube_url' => SiteSetting::get(SiteSetting::KEY_RUTUBE_URL, $siteContact['rutube_url']),
                'email' => SiteSetting::get(SiteSetting::KEY_EMAIL, $siteContact['email']),
                'phone' => SiteSetting::get(SiteSetting::KEY_PHONE, $siteContact['phone']),
                'schedule_weekdays' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_WEEKDAYS, $siteContact['schedule_weekdays']),
                'schedule_events' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_EVENTS, $siteContact['schedule_events']),
                'schedule_note' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_NOTE, $siteContact['schedule_note']),
                'company_name' => SiteSetting::get(SiteSetting::KEY_COMPANY_NAME, $siteContact['company_name']),
                'inn' => SiteSetting::get(SiteSetting::KEY_INN, $siteContact['inn']),
                'kpp' => SiteSetting::get(SiteSetting::KEY_KPP, $siteContact['kpp']),
                'ogrn' => SiteSetting::get(SiteSetting::KEY_OGRN, $siteContact['ogrn']),
            ];
        }
        View::share('siteContact', $siteContact);
    }
}
