<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Models\Product;
use App\Models\SiteSetting;

class ProductController extends Controller
{
    /**
     * Каталог товаров (сетка карточек).
     */
    public function index()
    {
        $query = Product::active()
            ->with(['coverMedia', 'media', 'variants']);

        if (request()->filled('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request()->filled('price_min')) {
            $query->where('price_amount', '>=', (float) request('price_min'));
        }
        if (request()->filled('price_max')) {
            $query->where('price_amount', '<=', (float) request('price_max'));
        }

        $sort = request('sort');
        if ($sort === 'price_asc') {
            $query->orderBy('price_amount', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price_amount', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        $shopHeroOverlayOpacity = SiteSetting::get(SiteSetting::KEY_SHOP_HERO_OVERLAY_OPACITY, '0.5');
        $shopHeroSlide1 = $this->shopHeroSlideUrl(SiteSetting::KEY_SHOP_HERO_SLIDE_1_MEDIA_ID, SiteSetting::KEY_SHOP_HERO_SLIDE_1);
        $shopHeroSlide2 = $this->shopHeroSlideUrl(SiteSetting::KEY_SHOP_HERO_SLIDE_2_MEDIA_ID, SiteSetting::KEY_SHOP_HERO_SLIDE_2);
        $shopHeroSlide3 = $this->shopHeroSlideUrl(SiteSetting::KEY_SHOP_HERO_SLIDE_3_MEDIA_ID, SiteSetting::KEY_SHOP_HERO_SLIDE_3);

        return view('shop.index', compact('products', 'shopHeroOverlayOpacity', 'shopHeroSlide1', 'shopHeroSlide2', 'shopHeroSlide3'));
    }

    /**
     * URL слайда: из media_id (приоритет) или из старого ключа (URL строка).
     */
    private function shopHeroSlideUrl(string $mediaIdKey, string $legacyUrlKey): string
    {
        $mediaId = SiteSetting::get($mediaIdKey);
        if ($mediaId && $asset = MediaAsset::find($mediaId)) {
            return $asset->url ?? '';
        }
        return (string) SiteSetting::get($legacyUrlKey, '');
    }

    /**
     * Карточка товара: карусель фото, описание, цена, атрибуты.
     */
    public function show(Product $product)
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load(['coverMedia', 'media', 'variants']);

        return view('shop.show', compact('product'));
    }
}
