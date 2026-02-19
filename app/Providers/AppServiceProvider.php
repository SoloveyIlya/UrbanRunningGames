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
use App\Observers\AuditableObserver;
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
    }
}
