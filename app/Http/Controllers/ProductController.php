<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Каталог товаров (сетка карточек).
     */
    public function index()
    {
        $products = Product::active()
            ->with(['coverMedia', 'media', 'variants'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('shop.index', compact('products'));
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
