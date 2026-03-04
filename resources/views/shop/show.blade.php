@extends('layouts.app')

@section('title', $product->name . ' - Магазин - Urban Running Games')

@php
    $galleryImages = $product->media->isEmpty()
        ? ( $product->cover_url ? [['url' => $product->cover_url, 'thumb' => $product->cover_url] ] : [] )
        : $product->media->map(fn ($m) => ['url' => $m->url, 'thumb' => $m->thumbnail_url ?? $m->url])->toArray();

    $variants = $product->variants;
    $hasSize = $variants->contains(fn ($v) => $v->size !== null && $v->size !== '');
    $hasColor = $variants->contains(fn ($v) => $v->color !== null && $v->color !== '');
    $sizes = $variants->pluck('size')->filter()->unique()->values();
    $colors = $variants->pluck('color')->filter()->unique()->values();

    $variantsMap = [];
    foreach ($variants as $v) {
        $key = ($v->size ?? '') . '|' . ($v->color ?? '');
        $variantsMap[$key] = ['id' => $v->id, 'price' => $v->display_price];
    }
    // Для каждого размера — список цветов, которые есть в наличии (чтобы блокировать остальные)
    $colorsBySize = [];
    foreach ($sizes as $size) {
        $colorsBySize[$size] = $variants->where('size', $size)->pluck('color')->filter()->unique()->values()->all();
    }
    $firstVariant = $variants->first();
    $initialPrice = $firstVariant ? $firstVariant->display_price : $product->display_price;
    $initialVariantId = $firstVariant ? $firstVariant->id : null;
@endphp

@section('content')
<div class="page-header page-header--product">
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
                <div class="product-detail-info-card">
                    @if($product->description)
                        <div class="product-detail-description content-block">
                            <h2 class="product-detail-info-title">Описание</h2>
                            <div class="content product-detail-description-text">{!! nl2br(e($product->description)) !!}</div>
                        </div>
                    @endif

                    <p class="product-detail-price" id="product_price_display">{{ $initialPrice }}</p>

                    @if($product->hasAttributes())
                        <div class="product-detail-attributes">
                            <form action="{{ route('cart.add') }}" method="POST" class="product-add-to-cart-form" id="productAddToCartForm">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="variant_id" id="product_variant_id" value="{{ $initialVariantId }}">

                                @if($hasSize)
                                    <div class="product-attribute-group product-attribute-group--size">
                                        <h3 class="product-detail-attributes-title">Размер</h3>
                                        <ul class="product-variants-list product-variants-list--inline">
                                            @foreach($sizes as $size)
                                                @php
                                                    $variantForSize = $hasColor
                                                        ? null
                                                        : $variants->firstWhere('size', $size);
                                                    $isFirstSize = $loop->first;
                                                    $isDefaultSize = $hasColor ? ($firstVariant && $firstVariant->size === $size) : ($variantForSize && $variantForSize->id === $firstVariant?->id);
                                                @endphp
                                                <li>
                                                    <label class="variant-option">
                                                        <input type="radio"
                                                               name="size_choice"
                                                               value="{{ $size }}"
                                                               data-variant-id="{{ $variantForSize?->id }}"
                                                               {{ $isDefaultSize ? 'checked' : '' }}>
                                                        <span class="variant-label">{{ $size }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($hasColor)
                                    <div class="product-attribute-group product-attribute-group--color">
                                        <h3 class="product-detail-attributes-title">Цвет</h3>
                                        <ul class="product-variants-list product-variants-list--inline">
                                            @foreach($colors as $color)
                                                @php
                                                    $variantForColor = $hasSize
                                                        ? null
                                                        : $variants->firstWhere('color', $color);
                                                    $isDefaultColor = $hasSize ? ($firstVariant && $firstVariant->color === $color) : ($variantForColor && $variantForColor->id === $firstVariant?->id);
                                                @endphp
                                                <li class="product-color-option" data-color="{{ $color }}">
                                                    <label class="variant-option">
                                                        <input type="radio"
                                                               name="color_choice"
                                                               value="{{ $color }}"
                                                               data-variant-id="{{ $variantForColor?->id }}"
                                                               {{ $isDefaultColor ? 'checked' : '' }}>
                                                        <span class="variant-label">{{ $color }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

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
    </div>
</section>

@if($product->hasAttributes())
@push('scripts')
<script>
(function() {
    var form = document.getElementById('productAddToCartForm');
    if (!form) return;
    var variantInput = document.getElementById('product_variant_id');
    var priceDisplay = document.getElementById('product_price_display');
    var variantsMap = @json($variantsMap);
    var colorsBySize = @json($colorsBySize);
    var hasSize = @json($hasSize);
    var hasColor = @json($hasColor);

    function getSelectedSize() {
        var r = form.querySelector('input[name="size_choice"]:checked');
        return r ? r.value : '';
    }
    function getSelectedColor() {
        var r = form.querySelector('input[name="color_choice"]:checked');
        return r ? r.value : '';
    }

    function setColorAvailability() {
        if (!hasSize || !hasColor) return;
        var size = getSelectedSize();
        var allowedColors;
        if (size) {
            allowedColors = colorsBySize[size] || [];
        } else {
            allowedColors = [];
            form.querySelectorAll('.product-color-option').forEach(function(li) {
                allowedColors.push(li.getAttribute('data-color'));
            });
        }
        form.querySelectorAll('.product-color-option').forEach(function(li) {
            var color = li.getAttribute('data-color');
            var radio = li.querySelector('input[name="color_choice"]');
            var label = li.querySelector('.variant-option');
            var available = allowedColors.indexOf(color) !== -1;
            radio.disabled = !available;
            if (label) label.classList.toggle('variant-option--disabled', !available);
            li.classList.toggle('product-color-option--disabled', !available);
        });
        var currentColor = getSelectedColor();
        var currentAllowed = currentColor && allowedColors.indexOf(currentColor) !== -1;
        if (!currentAllowed && allowedColors.length > 0) {
            var firstAvailable = form.querySelector('.product-color-option input[name="color_choice"]:not(:disabled)');
            if (firstAvailable) firstAvailable.checked = true;
        }
    }

    function updateVariantAndPrice() {
        setColorAvailability();
        var variantId = null;
        var price = '';

        if (hasSize && hasColor) {
            var key = getSelectedSize() + '|' + getSelectedColor();
            var found = variantsMap[key];
            if (found) {
                variantId = found.id;
                price = found.price;
            }
        } else if (hasSize) {
            var sizeRadio = form.querySelector('input[name="size_choice"]:checked');
            if (sizeRadio && sizeRadio.dataset.variantId) {
                variantId = parseInt(sizeRadio.dataset.variantId, 10);
                var v = variantsMap[getSelectedSize() + '|'];
                if (v) price = v.price;
            }
        } else if (hasColor) {
            var colorRadio = form.querySelector('input[name="color_choice"]:checked');
            if (colorRadio && colorRadio.dataset.variantId) {
                variantId = parseInt(colorRadio.dataset.variantId, 10);
                var v = variantsMap['|' + getSelectedColor()];
                if (v) price = v.price;
            }
        }

        if (variantInput) variantInput.value = variantId || '';
        if (priceDisplay && price) priceDisplay.textContent = price;
    }

    form.querySelectorAll('input[name="size_choice"], input[name="color_choice"]').forEach(function(inp) {
        inp.addEventListener('change', updateVariantAndPrice);
    });

    setColorAvailability();
    updateVariantAndPrice();
})();
</script>
@endpush
@endif

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
