@extends('layouts.app')

@section('title', $product->name . ' - Магазин - Urban Running Games')

@php
    $galleryImages = $product->media->isEmpty()
        ? ( $product->cover_url ? [['url' => $product->cover_url, 'thumb' => $product->cover_url] ] : [] )
        : $product->media->map(fn ($m) => ['url' => $m->url, 'thumb' => $m->thumbnail_url ?? $m->url])->toArray();

    $variants = $product->variants;
    $hasSize = $variants->contains(fn ($v) => $v->size !== null && $v->size !== '');
    $sizes = $variants->pluck('size')->filter()->unique()->values();
    $variantsMap = [];
    foreach ($variants as $v) {
        $key = ($v->size ?? '') . '|';
        $variantsMap[$key] = ['id' => $v->id, 'price' => $v->display_price];
    }
    $firstVariant = $variants->first();
    $initialPrice = $firstVariant ? $firstVariant->display_price : $product->display_price;
    $initialVariantId = $firstVariant ? $firstVariant->id : null;
@endphp

@section('content')
<div class="page-header page-header--product py-6 px-0 bg-[#111] text-gray-200">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <nav class="breadcrumb-nav flex items-center gap-1 text-sm text-white/80 mb-2" aria-label="Хлебные крошки">
            <a href="{{ route('shop.index') }}">Магазин</a>
            <span class="breadcrumb-sep">/</span>
            <span>{{ $product->name }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold italic uppercase tracking-wide text-white m-0">{{ $product->name }}</h1>
    </div>
</div>

<section class="product-detail-section py-12 md:py-16">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <div class="product-detail-grid grid grid-cols-1 lg:grid-cols-[1fr_1fr] gap-8 lg:gap-12">
            <div class="product-detail-gallery">
                @if(count($galleryImages) > 0)
                    <div class="product-carousel" id="productCarousel">
                        <div class="product-carousel-inner">
                            @foreach($galleryImages as $index => $img)
                                <div class="product-carousel-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                    <button type="button" class="product-gallery-zoom-trigger" data-index="{{ $index }}" data-src="{{ $img['url'] }}" aria-label="Увеличить изображение">
                                        <img src="{{ $img['url'] }}" alt="{{ $product->name }} — фото {{ $index + 1 }}" width="600" height="600">
                                        <span class="product-gallery-zoom-hint">Увеличить</span>
                                    </button>
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
                    <div class="product-lightbox" id="productLightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Просмотр изображения">
                        <div class="product-lightbox-backdrop" id="productLightboxBackdrop"></div>
                        <div class="product-lightbox-toolbar">
                            <button type="button" class="product-lightbox-btn product-lightbox-zoom-out" id="productLightboxZoomOut" aria-label="Уменьшить">−</button>
                            <span class="product-lightbox-zoom-value" id="productLightboxZoomValue">100%</span>
                            <button type="button" class="product-lightbox-btn product-lightbox-zoom-in" id="productLightboxZoomIn" aria-label="Увеличить">+</button>
                            @if(count($galleryImages) > 1)
                                <button type="button" class="product-lightbox-btn product-lightbox-prev" id="productLightboxPrev" aria-label="Предыдущее">‹</button>
                                <button type="button" class="product-lightbox-btn product-lightbox-next" id="productLightboxNext" aria-label="Следующее">›</button>
                            @endif
                            <button type="button" class="product-lightbox-btn product-lightbox-close" id="productLightboxClose" aria-label="Закрыть">×</button>
                        </div>
                        <div class="product-lightbox-stage" id="productLightboxStage">
                            <img class="product-lightbox-img" id="productLightboxImg" src="" alt="">
                        </div>
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
                                                    $variantForSize = $variants->firstWhere('size', $size);
                                                    $isDefaultSize = $variantForSize && $variantForSize->id === $firstVariant?->id;
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

                                <div class="product-add-qty">
                                    <label for="qty">Количество:</label>
                                    <input type="number" id="qty" name="quantity" value="1" min="1" max="99">
                                </div>
                                <div class="product-detail-actions">
                                    <button type="submit" class="btn btn-primary product-btn-add-to-cart">Добавить в корзину</button>
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
                                <button type="submit" class="btn btn-primary product-btn-add-to-cart">Добавить в корзину</button>
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
    var hasSize = @json($hasSize);

    function getSelectedSize() {
        var r = form.querySelector('input[name="size_choice"]:checked');
        return r ? r.value : '';
    }

    function updateVariantAndPrice() {
        var variantId = null;
        var price = '';
        if (hasSize) {
            var sizeRadio = form.querySelector('input[name="size_choice"]:checked');
            if (sizeRadio && sizeRadio.dataset.variantId) {
                variantId = parseInt(sizeRadio.dataset.variantId, 10);
                var v = variantsMap[getSelectedSize() + '|'];
                if (v) price = v.price;
            }
        }
        if (variantInput) variantInput.value = variantId || '';
        if (priceDisplay && price) priceDisplay.textContent = price;
    }

    form.querySelectorAll('input[name="size_choice"]').forEach(function(inp) {
        inp.addEventListener('change', updateVariantAndPrice);
    });

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

@if(count($galleryImages) > 0)
@push('scripts')
<script>
(function() {
    var lightbox = document.getElementById('productLightbox');
    var backdrop = document.getElementById('productLightboxBackdrop');
    var stage = document.getElementById('productLightboxStage');
    var imgEl = document.getElementById('productLightboxImg');
    var zoomValueEl = document.getElementById('productLightboxZoomValue');
    var zoomInBtn = document.getElementById('productLightboxZoomIn');
    var zoomOutBtn = document.getElementById('productLightboxZoomOut');
    var closeBtn = document.getElementById('productLightboxClose');
    var prevBtn = document.getElementById('productLightboxPrev');
    var nextBtn = document.getElementById('productLightboxNext');
    var triggers = document.querySelectorAll('.product-gallery-zoom-trigger');
    if (!lightbox || !imgEl || !triggers.length) return;

    var galleryUrls = [];
    triggers.forEach(function(t) { galleryUrls.push(t.getAttribute('data-src')); });
    var currentIndex = 0;
    var scale = 1;
    var minScale = 0.5;
    var maxScale = 4;
    var step = 0.25;
    var posX = 0, posY = 0;
    var isDragging = false;
    var startX, startY, startPosX, startPosY;

    function setScale(s) {
        scale = Math.max(minScale, Math.min(maxScale, s));
        if (zoomValueEl) zoomValueEl.textContent = Math.round(scale * 100) + '%';
        imgEl.style.transform = 'translate(' + posX + 'px, ' + posY + 'px) scale(' + scale + ')';
    }
    function setImage(index) {
        currentIndex = (index + galleryUrls.length) % galleryUrls.length;
        var src = galleryUrls[currentIndex];
        imgEl.src = src;
        imgEl.alt = document.querySelector('.product-gallery-zoom-trigger[data-index="' + currentIndex + '"] img')?.alt || 'Фото ' + (currentIndex + 1);
        scale = 1;
        posX = 0;
        posY = 0;
        setScale(1);
    }
    function openLightbox(index) {
        currentIndex = index >= 0 ? index : 0;
        setImage(currentIndex);
        lightbox.setAttribute('aria-hidden', 'false');
        lightbox.classList.add('product-lightbox--open');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }
    function closeLightbox() {
        lightbox.setAttribute('aria-hidden', 'true');
        lightbox.classList.remove('product-lightbox--open');
        document.body.style.overflow = '';
    }

    triggers.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var idx = parseInt(this.getAttribute('data-index'), 10);
            openLightbox(isNaN(idx) ? 0 : idx);
        });
    });
    if (backdrop) backdrop.addEventListener('click', closeLightbox);
    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
    if (zoomInBtn) zoomInBtn.addEventListener('click', function() { setScale(scale + step); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function() { setScale(scale - step); });
    if (prevBtn) prevBtn.addEventListener('click', function() { setImage(currentIndex - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function() { setImage(currentIndex + 1); });

    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('product-lightbox--open')) return;
        if (e.key === 'Escape') { closeLightbox(); return; }
        if (e.key === 'ArrowLeft') { setImage(currentIndex - 1); return; }
        if (e.key === 'ArrowRight') { setImage(currentIndex + 1); return; }
    });

    stage.addEventListener('mousedown', function(e) {
        if (e.target !== imgEl) return;
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startPosX = posX;
        startPosY = posY;
    });
    stage.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        posX = startPosX + (e.clientX - startX);
        posY = startPosY + (e.clientY - startY);
        imgEl.style.transform = 'translate(' + posX + 'px, ' + posY + 'px) scale(' + scale + ')';
    });
    stage.addEventListener('mouseup', function() { isDragging = false; });
    stage.addEventListener('mouseleave', function() { isDragging = false; });

    stage.addEventListener('wheel', function(e) {
        if (!lightbox.classList.contains('product-lightbox--open')) return;
        e.preventDefault();
        if (e.deltaY < 0) setScale(scale + step);
        else setScale(scale - step);
    }, { passive: false });
})();
</script>
@endpush
@endif
@endsection
