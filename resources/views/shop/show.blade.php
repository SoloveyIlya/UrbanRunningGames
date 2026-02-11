@extends('layouts.app')

@section('title', $product->name . ' - Магазин - Urban Running Games')

@php
    $galleryImages = $product->media->isEmpty()
        ? ( $product->cover_url ? [['url' => $product->cover_url, 'thumb' => $product->cover_url] ] : [] )
        : $product->media->map(fn ($m) => ['url' => $m->url, 'thumb' => $m->thumbnail_url ?? $m->url])->toArray();
@endphp

@section('content')
<div class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="{{ route('shop.index') }}">Магазин</a>
            <span class="breadcrumb-sep">/</span>
            <span>{{ $product->name }}</span>
        </nav>
        <h1>{{ $product->name }}</h1>
    </div>
</div>

<section class="product-detail-section">
    <div class="container">
        <div class="product-detail-grid">
            <div class="product-detail-gallery">
                @if(count($galleryImages) > 0)
                    <div class="product-carousel" id="productCarousel">
                        <div class="product-carousel-inner">
                            @foreach($galleryImages as $index => $img)
                                <div class="product-carousel-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                    <img src="{{ $img['url'] }}" alt="{{ $product->name }} — фото {{ $index + 1 }}" width="600" height="600">
                                </div>
                            @endforeach
                        </div>
                        @if(count($galleryImages) > 1)
                            <button type="button" class="product-carousel-btn product-carousel-prev" aria-label="Предыдущее фото">‹</button>
                            <button type="button" class="product-carousel-btn product-carousel-next" aria-label="Следующее фото">›</button>
                            <div class="product-carousel-dots">
                                @foreach($galleryImages as $index => $img)
                                    <button type="button" class="product-carousel-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" aria-label="Фото {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="product-carousel-placeholder">
                        <span>Нет фото</span>
                    </div>
                @endif
            </div>
            <div class="product-detail-info">
                @if($product->description)
                    <div class="product-detail-description content-block">
                        <h2>Описание</h2>
                        <div class="content">{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif
                <p class="product-detail-price">{{ $product->display_price }}</p>
                @if($product->hasAttributes())
                    <div class="product-detail-attributes">
                        <h3>Доступные варианты</h3>
                        <form action="{{ route('cart.add') }}" method="POST" class="product-add-to-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <ul class="product-variants-list product-variants-choose">
                                @foreach($product->variants as $variant)
                                    <li>
                                        <label class="variant-option">
                                            <input type="radio" name="variant_id" value="{{ $variant->id }}" required>
                                            <span class="variant-label">{{ $variant->attribute_label }}</span>
                                            @if($variant->price_override !== null)
                                                <span class="variant-price">{{ $variant->display_price }}</span>
                                            @endif
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="product-add-qty">
                                <label for="qty">Количество:</label>
                                <input type="number" id="qty" name="quantity" value="1" min="1" max="99">
                            </div>
                            <div class="product-detail-actions">
                                <button type="submit" class="btn btn-primary">В корзину</button>
                                <a href="{{ route('shop.index') }}" class="btn btn-secondary">← В каталог</a>
                            </div>
                        </form>
                    </div>
                @else
                    <form action="{{ route('cart.add') }}" method="POST" class="product-add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="product-add-qty">
                            <label for="qty">Количество:</label>
                            <input type="number" id="qty" name="quantity" value="1" min="1" max="99">
                        </div>
                        <div class="product-detail-actions">
                            <button type="submit" class="btn btn-primary">В корзину</button>
                            <a href="{{ route('shop.index') }}" class="btn btn-secondary">← В каталог</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>

@if(count($galleryImages) > 1)
@push('scripts')
<script>
(function() {
    var carousel = document.getElementById('productCarousel');
    if (!carousel) return;
    var slides = carousel.querySelectorAll('.product-carousel-slide');
    var dots = carousel.querySelectorAll('.product-carousel-dot');
    var prev = carousel.querySelector('.product-carousel-prev');
    var next = carousel.querySelector('.product-carousel-next');
    var total = slides.length;
    var current = 0;

    function goTo(index) {
        current = (index + total) % total;
        slides.forEach(function(s, i) { s.classList.toggle('active', i === current); });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
    }
    if (prev) prev.addEventListener('click', function() { goTo(current - 1); });
    if (next) next.addEventListener('click', function() { goTo(current + 1); });
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() { goTo(parseInt(this.getAttribute('data-index'), 10)); });
    });
})();
</script>
@endpush
@endif
@endsection
