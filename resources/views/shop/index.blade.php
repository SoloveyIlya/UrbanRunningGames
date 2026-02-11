@extends('layouts.app')

@section('title', 'Магазин мерча - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Магазин мерча</h1>
        <p class="page-header-sub">Футболки, брендированная экипировка и сувениры</p>
    </div>
</div>

<section class="shop-section">
    <div class="container">
        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    <a href="{{ route('shop.show', $product) }}" class="product-card">
                        <div class="product-card-image">
                            @if($product->cover_url)
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="400" height="400">
                            @else
                                <div class="product-card-placeholder">
                                    <span>Нет фото</span>
                                </div>
                            @endif
                        </div>
                        <div class="product-card-info">
                            <h3 class="product-card-title">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="product-card-description">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 80) }}</p>
                            @endif
                            <p class="product-card-price">{{ $product->display_price }}</p>
                            @if($product->hasAttributes())
                                <p class="product-card-attributes">
                                    Размеры / цвета в наличии
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            @if($products->hasPages())
                <div class="pagination-wrap">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <p>В каталоге пока нет товаров. Следите за обновлениями!</p>
            </div>
        @endif
    </div>
</section>
@endsection
