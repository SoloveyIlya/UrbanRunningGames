@extends('layouts.app')

@section('title', 'Магазин - Urban Running Games')

@section('content')
<div class="shop-page-wrap min-h-screen text-gray-100 relative z-10">
    <div class="shop-page-inner max-w-[1200px] mx-auto">
        {{-- Breadcrumb — по макету: 16px/20px, #8D49EE --}}
        <nav class="shop-breadcrumb mb-0" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current" aria-current="page">Магазин</span>
        </nav>

        {{-- Title — по макету --}}
        <h1 class="shop-title">{{ $shopPageTitle ?? 'SPRUT STYLE STORE' }}</h1>

        {{-- Подзаголовок под заголовком — по макету --}}
        <p class="shop-subtitle">{{ $shopPageSubtitle ?? '' }}</p>

        {{-- Фильтры по типам (Frame 102/101): под заголовком — стекло + активная как btn--race --}}
        <div class="shop-toolbar-row">
            <nav class="shop-filters-row" aria-label="Типы товаров">
                <a href="{{ route('shop.index') }}"
                   class="shop-filter-btn {{ ($activeType ?? '') === '' ? 'shop-filter-btn--active' : '' }}">Все товары</a>
                @foreach($productTypes ?? [] as $type)
                    <a href="{{ route('shop.index', ['type' => $type->slug]) }}"
                       class="shop-filter-btn {{ ($activeType ?? '') === $type->slug ? 'shop-filter-btn--active' : '' }}">
                        {{ $type->label }}
                    </a>
                @endforeach
            </nav>

            <a href="{{ route('cart.index') }}"
               class="shop-cart-link"
               aria-label="Перейти в корзину"
               title="Корзина">
                <svg class="shop-cart-link__icon" width="32" height="26" viewBox="0 0 32 26" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M14.1395 13C14.1395 12.3846 13.6398 11.8857 13.0233 11.8857C12.4068 11.8857 11.907 12.3846 11.907 13V18.9428C11.907 19.5582 12.4068 20.0571 13.0233 20.0571C13.6398 20.0571 14.1395 19.5582 14.1395 18.9428V13Z" fill="white" fill-opacity="0.6"/>
                    <path d="M20.093 13C20.093 12.3846 19.5932 11.8857 18.9767 11.8857C18.3602 11.8857 17.8605 12.3846 17.8605 13V18.9428C17.8605 19.5582 18.3602 20.0571 18.9767 20.0571C19.5932 20.0571 20.093 19.5582 20.093 18.9428V13Z" fill="white" fill-opacity="0.6"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.2545 0.326366C20.8186 -0.108789 20.1118 -0.108789 19.6758 0.326366C19.2399 0.761521 19.2399 1.46705 19.6758 1.9022L23.7237 5.94286H8.63748L12.6854 1.90222C13.1213 1.46707 13.1213 0.761543 12.6854 0.326389C12.2494 -0.108766 11.5426 -0.108766 11.1067 0.326389L5.48016 5.94286H1.11628C0.499775 5.94286 0 6.44174 0 7.05714C0 7.67255 0.499775 8.17143 1.11628 8.17143H1.92642C2.26341 9.4435 2.69927 10.9101 3.20641 12.6166L4.73184 17.7497C5.33785 19.7899 5.70936 21.0406 6.36054 22.0577C7.48161 23.8086 9.20358 25.0917 11.2041 25.6665C12.366 26.0005 13.6729 26.0003 15.8048 26H16.1952C18.3271 26.0003 19.634 26.0005 20.7959 25.6665C22.7964 25.0917 24.5184 23.8086 25.6395 22.0577C26.2906 21.0406 26.6622 19.7899 27.2682 17.7496L28.7935 12.617C29.3006 10.9104 29.7366 9.44352 30.0736 8.17143H30.8837C31.5002 8.17143 32 7.67255 32 7.05714C32 6.44174 31.5002 5.94286 30.8837 5.94286H26.8811L21.2545 0.326366ZM5.35432 12.0082C4.91615 10.5338 4.54141 9.27232 4.23868 8.17143H27.7613C27.4586 9.27232 27.0839 10.5338 26.6457 12.0082L25.1833 16.929C24.5037 19.2158 24.2183 20.1391 23.7583 20.8575C22.9297 22.1517 21.6569 23.1 20.1783 23.525C19.3575 23.7608 18.3895 23.7714 16 23.7714C13.6105 23.7714 12.6425 23.7608 11.8217 23.525C10.3431 23.1 9.07032 22.1517 8.24171 20.8575C7.78174 20.1391 7.49628 19.2158 6.8167 16.929L5.35432 12.0082Z" fill="white" fill-opacity="0.6"/>
                </svg>
                @php $cartCount = \App\Http\Controllers\CartController::getCount(); @endphp
                @if($cartCount > 0)
                    <span class="shop-cart-link__count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        {{-- Product grid — карточки без фона и границ, по макету --}}
        @if($products->count() > 0)
            <div class="shop-products-grid">
                @foreach($products as $product)
                    <a href="{{ route('shop.show', $product) }}" class="shop-product-card js-product-modal-trigger" data-product-id="{{ $product->id }}">
                        <div class="shop-product-card__img">
                            @if($product->cover_url)
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                            @else
                                <span class="shop-product-card__no-photo">Нет фото</span>
                            @endif
                        </div>
                        <h3 class="shop-product-card__title">{{ $product->name }}</h3>
                        <p class="shop-product-card__price">{{ $product->display_price }}</p>
                    </a>
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="mt-10 flex justify-center pagination-wrap">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="shop-empty-state">
                <p class="shop-empty-state__text">В этой категории пока нет товаров. Попробуйте другую или загляните позже.</p>
                <a href="{{ route('shop.index') }}" class="shop-empty-state__btn">Все товары</a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function() {
    var modal = document.getElementById('productModal');
    var backdrop = document.querySelector('.product-modal__backdrop');
    var closeBtn = document.querySelector('.product-modal__close');
    var triggers = document.querySelectorAll('.js-product-modal-trigger');
    var mainImg = document.getElementById('productModalMainImg');
    var noPhoto = document.getElementById('productModalNoPhoto');
    var thumbsWrap = document.getElementById('productModalThumbs');
    var titleEl = document.getElementById('productModalTitle');
    var descEl = document.getElementById('productModalDescription');
    var priceEl = document.getElementById('productModalPrice');
    var form = document.getElementById('productModalForm');
    var productIdInput = document.getElementById('productModalProductId');
    var variantIdInput = document.getElementById('productModalVariantId');
    var errorEl = document.getElementById('productModalError');
    var genderWrap = document.getElementById('productModalGender');
    var sizesWrap = document.getElementById('productModalSizesWrap');
    var sizesContainer = document.getElementById('productModalSizes');
    var addToCartUrl = '{{ route("cart.add") }}';
    var csrfToken = document.querySelector('input[name="_token"]') && document.querySelector('input[name="_token"]').value;

    var currentData = null;
    var selectedGender = null;
    var selectedSize = null;

    function openModal() {
        var scrollY = window.scrollY || window.pageYOffset;
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('product-modal-open');
        document.body.classList.add('product-modal-open');
        document.body.style.overflow = 'hidden';
        document.body.dataset.modalScrollY = String(scrollY);
        if (closeBtn) closeBtn.focus();
    }

    function closeModal() {
        var scrollY = document.body.dataset.modalScrollY ? parseInt(document.body.dataset.modalScrollY, 10) : 0;
        modal.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('product-modal-open');
        document.body.classList.remove('product-modal-open');
        document.body.style.overflow = '';
        delete document.body.dataset.modalScrollY;
        window.scrollTo(0, scrollY);
    }

    function setActiveThumb(index) {
        if (!thumbsWrap) return;
        thumbsWrap.querySelectorAll('.product-modal__thumb').forEach(function(el, i) {
            el.classList.toggle('is-active', i === index);
        });
    }

    function setMainImage(url, alt) {
        if (mainImg) {
            mainImg.src = url;
            mainImg.alt = alt || '';
            mainImg.style.display = url ? '' : 'none';
        }
        if (noPhoto) noPhoto.style.display = url ? 'none' : 'flex';
    }

    function renderThumbs(gallery, productName) {
        if (!thumbsWrap) return;
        thumbsWrap.innerHTML = '';
        if (!gallery || gallery.length === 0) return;
        gallery.forEach(function(img, i) {
            var div = document.createElement('div');
            div.className = 'product-modal__thumb' + (i === 0 ? ' is-active' : '');
            div.setAttribute('data-index', i);
            var im = document.createElement('img');
            im.src = img.thumb || img.url;
            im.alt = productName + ' — фото ' + (i + 1);
            div.appendChild(im);
            div.addEventListener('click', function() {
                var idx = parseInt(this.getAttribute('data-index'), 10);
                setMainImage(gallery[idx].url, im.alt);
                setActiveThumb(idx);
            });
            thumbsWrap.appendChild(div);
        });
    }

    function findVariant(size, gender) {
        if (!currentData || !currentData.variants_map) return null;
        var key = (size || '') + '|' + (gender || '');
        if (currentData.variants_map[key]) return currentData.variants_map[key];
        var keyNoGender = (size || '') + '|';
        if (currentData.variants_map[keyNoGender]) return currentData.variants_map[keyNoGender];
        var keyNoSize = '|' + (gender || '');
        if (currentData.variants_map[keyNoSize]) return currentData.variants_map[keyNoSize];
        return null;
    }

    function getSizesForGender(gender) {
        if (!currentData || !currentData.variants_map) return [];
        var sizes = [];
        for (var key in currentData.variants_map) {
            var v = currentData.variants_map[key];
            var s = v.size;
            if (!s || s === '') continue;
            var g = v.gender || '';
            if (!gender || g === '' || g === gender) {
                if (sizes.indexOf(s) === -1) sizes.push(s);
            }
        }
        return sizes;
    }

    function updateSizeButtons() {
        if (!sizesContainer || !currentData) return;
        var available = getSizesForGender(selectedGender);
        var btns = sizesContainer.querySelectorAll('.product-modal__opt--size');
        var activeStillVisible = false;
        btns.forEach(function(btn) {
            var s = btn.getAttribute('data-size');
            var isAvailable = available.indexOf(s) !== -1;
            btn.style.display = isAvailable ? '' : 'none';
            btn.disabled = !isAvailable;
            if (!isAvailable && btn.classList.contains('is-active')) {
                btn.classList.remove('is-active');
            }
            if (isAvailable && btn.classList.contains('is-active')) {
                activeStillVisible = true;
            }
        });
        if (!activeStillVisible && available.length > 0) {
            btns.forEach(function(btn) {
                if (btn.getAttribute('data-size') === available[0]) {
                    btn.classList.add('is-active');
                    selectedSize = available[0];
                }
            });
        }
        if (available.length === 0) selectedSize = null;
    }

    function updateVariantAndPrice() {
        if (!currentData) return;
        updateSizeButtons();
        var v = findVariant(selectedSize, selectedGender);
        if (v) {
            if (variantIdInput) variantIdInput.value = v.id;
            if (priceEl) priceEl.textContent = v.price;
        } else {
            if (priceEl) priceEl.textContent = currentData.initial_price;
        }
    }

    function renderSizes(sizes) {
        if (!sizesContainer) return;
        sizesContainer.innerHTML = '';
        if (!sizes || sizes.length === 0) return;
        var first = true;
        sizes.forEach(function(size) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'product-modal__opt product-modal__opt--size' + (first ? ' is-active' : '');
            btn.textContent = size;
            btn.setAttribute('data-size', size);
            btn.addEventListener('click', function() {
                sizesContainer.querySelectorAll('.product-modal__opt--size').forEach(function(b) { b.classList.remove('is-active'); });
                this.classList.add('is-active');
                selectedSize = this.getAttribute('data-size');
                updateVariantAndPrice();
            });
            sizesContainer.appendChild(btn);
            if (first) selectedSize = size;
            first = false;
        });
    }

    triggers.forEach(function(a) {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-product-id');
            if (!id) return;
            fetch('{{ url("/shop/product") }}/' + id + '/data', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                currentData = data;
                selectedGender = data.initial_gender || null;
                selectedSize = null;
                if (productIdInput) productIdInput.value = data.id;
                if (titleEl) titleEl.textContent = data.name;
                if (descEl) descEl.textContent = data.description || '';
                if (priceEl) priceEl.textContent = data.initial_price;
                if (variantIdInput) variantIdInput.value = data.initial_variant_id || '';

                if (data.gallery && data.gallery.length > 0) {
                    setMainImage(data.gallery[0].url, data.name);
                    renderThumbs(data.gallery, data.name);
                } else {
                    setMainImage('', data.name);
                    if (thumbsWrap) thumbsWrap.innerHTML = '';
                }

                if (data.has_gender && data.genders && data.genders.length > 0) {
                    if (genderWrap) genderWrap.style.display = 'block';
                    var genderBtns = genderWrap ? genderWrap.querySelectorAll('.product-modal__opt--gender') : [];
                    genderBtns.forEach(function(btn) {
                        btn.classList.remove('is-active');
                        if (btn.getAttribute('data-value') === selectedGender) {
                            btn.classList.add('is-active');
                        } else if (!selectedGender && btn.getAttribute('data-value') === data.genders[0]) {
                            btn.classList.add('is-active');
                            selectedGender = data.genders[0];
                        }
                    });
                } else {
                    if (genderWrap) genderWrap.style.display = 'none';
                    selectedGender = null;
                }

                if (data.has_size && data.sizes && data.sizes.length > 0) {
                    sizesWrap.style.display = 'block';
                    renderSizes(data.sizes);
                } else {
                    sizesWrap.style.display = 'none';
                    selectedSize = null;
                }

                updateVariantAndPrice();
                if (errorEl) errorEl.style.display = 'none';
                openModal();
            })
            .catch(function() {
                window.location.href = '{{ url("/shop/product") }}/' + id;
            });
        });
    });

    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    var genderOpts = document.querySelectorAll('.product-modal__opt--gender');
    genderOpts.forEach(function(btn) {
        btn.addEventListener('click', function() {
            genderOpts.forEach(function(b) { b.classList.remove('is-active'); });
            this.classList.add('is-active');
            selectedGender = this.getAttribute('data-value');
            updateVariantAndPrice();
        });
    });

    modal.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') closeModal();
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (errorEl) errorEl.style.display = 'none';
            if (currentData && currentData.has_size && currentData.sizes && currentData.sizes.length > 0 && (!variantIdInput || !variantIdInput.value)) {
                if (errorEl) {
                    errorEl.textContent = 'Выберите размер.';
                    errorEl.style.display = 'block';
                }
                return;
            }
            var fd = new FormData(form);
            fetch(addToCartUrl, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
            .then(function(res) {
                if (res.ok && res.data.success) {
                    closeModal();
                    var countEl = document.querySelector('.shop-cart-link__count');
                    if (countEl) countEl.textContent = res.data.cart_count;
                    else if (res.data.cart_count > 0) {
                        var link = document.querySelector('.shop-cart-link');
                        if (link) {
                            var span = document.createElement('span');
                            span.className = 'shop-cart-link__count';
                            span.textContent = res.data.cart_count;
                            link.appendChild(span);
                        }
                    }
                } else {
                    if (errorEl) {
                        errorEl.textContent = res.data && res.data.message ? res.data.message : 'Ошибка добавления в корзину.';
                        errorEl.style.display = 'block';
                    }
                }
            })
            .catch(function() {
                form.submit();
            });
        });
    }
})();
</script>
@endpush

@push('modals')
{{-- Модальное окно товара: вне main, не создаёт отступов, перекрывает навбар --}}
<div id="productModal" class="product-modal" role="dialog" aria-modal="true" aria-labelledby="productModalTitle" aria-hidden="true">
    <div class="product-modal__backdrop js-product-modal-close"></div>
    <div class="product-modal__box">
        <button type="button" class="product-modal__close js-product-modal-close" aria-label="Закрыть">
            <span>×</span>
        </button>
        <div class="product-modal__body">
            <div class="product-modal__gallery">
                <div class="product-modal__main-img-wrap">
                    <img class="product-modal__main-img" src="" alt="" id="productModalMainImg">
                    <span class="product-modal__no-photo" id="productModalNoPhoto" style="display: none;">Нет фото</span>
                </div>
                <div class="product-modal__thumbs" id="productModalThumbs"></div>
            </div>
            <div class="product-modal__info">
                <h2 class="product-modal__title" id="productModalTitle"></h2>
                <p class="product-modal__description" id="productModalDescription"></p>
                <div class="product-modal__gender product-modal__field" id="productModalGender" style="display: none;">
                    <span class="product-modal__label">Пол</span>
                    <div class="product-modal__options">
                        <button type="button" class="product-modal__opt product-modal__opt--gender" data-value="M" title="Мужской">M</button>
                        <button type="button" class="product-modal__opt product-modal__opt--gender" data-value="Ж" title="Женский">Ж</button>
                    </div>
                </div>
                <div class="product-modal__sizes product-modal__field" id="productModalSizesWrap" style="display: none;">
                    <span class="product-modal__label">Размеры</span>
                    <div class="product-modal__options" id="productModalSizes"></div>
                </div>
                <p class="product-modal__price" id="productModalPrice"></p>
                <form class="product-modal__form" id="productModalForm" action="{{ route('cart.add') }}" method="post">
                    @csrf
                    <input type="hidden" name="product_id" id="productModalProductId" value="">
                    <input type="hidden" name="variant_id" id="productModalVariantId" value="">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="product-modal__add-btn">
                        <span>Добавить в корзину</span>
                        <svg class="product-modal__add-btn-icon" width="20" height="16" viewBox="0 0 32 26" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M14.1395 13C14.1395 12.3846 13.6398 11.8857 13.0233 11.8857C12.4068 11.8857 11.907 12.3846 11.907 13V18.9428C11.907 19.5582 12.4068 20.0571 13.0233 20.0571C13.6398 20.0571 14.1395 19.5582 14.1395 18.9428V13Z" fill="currentColor"/>
                            <path d="M20.093 13C20.093 12.3846 19.5932 11.8857 18.9767 11.8857C18.3602 11.8857 17.8605 12.3846 17.8605 13V18.9428C17.8605 19.5582 18.3602 20.0571 18.9767 20.0571C19.5932 20.0571 20.093 19.5582 20.093 18.9428V13Z" fill="currentColor"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.2545 0.326366C20.8186 -0.108789 20.1118 -0.108789 19.6758 0.326366C19.2399 0.761521 19.2399 1.46705 19.6758 1.9022L23.7237 5.94286H8.63748L12.6854 1.90222C13.1213 1.46707 13.1213 0.761543 12.6854 0.326389C12.2494 -0.108766 11.5426 -0.108766 11.1067 0.326389L5.48016 5.94286H1.11628C0.499775 5.94286 0 6.44174 0 7.05714C0 7.67255 0.499775 8.17143 1.11628 8.17143H1.92642C2.26341 9.4435 2.69927 10.9101 3.20641 12.6166L4.73184 17.7497C5.33785 19.7899 5.70936 21.0406 6.36054 22.0577C7.48161 23.8086 9.20358 25.0917 11.2041 25.6665C12.366 26.0005 13.6729 26.0003 15.8048 26H16.1952C18.3271 26.0003 19.634 26.0005 20.7959 25.6665C22.7964 25.0917 24.5184 23.8086 25.6395 22.0577C26.2906 21.0406 26.6622 19.7899 27.2682 17.7496L28.7935 12.617C29.3006 10.9104 29.7366 9.44352 30.0736 8.17143H30.8837C31.5002 8.17143 32 7.67255 32 7.05714C32 6.44174 31.5002 5.94286 30.8837 5.94286H26.8811L21.2545 0.326366ZM5.35432 12.0082C4.91615 10.5338 4.54141 9.27232 4.23868 8.17143H27.7613C27.4586 9.27232 27.0839 10.5338 26.6457 12.0082L25.1833 16.929C24.5037 19.2158 24.2183 20.1391 23.7583 20.8575C22.9297 22.1517 21.6569 23.1 20.1783 23.525C19.3575 23.7608 18.3895 23.7714 16 23.7714C13.6105 23.7714 12.6425 23.7608 11.8217 23.525C10.3431 23.1 9.07032 22.1517 8.24171 20.8575C7.78174 20.1391 7.49628 19.2158 6.8167 16.929L5.35432 12.0082Z" fill="currentColor"/>
                        </svg>
                    </button>
                </form>
                <p class="product-modal__error" id="productModalError" style="display: none;"></p>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection
