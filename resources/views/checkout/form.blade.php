@extends('layouts.app')

@section('title', 'Оформление заявки - Urban Running Games')

@section('content')
<div class="checkout-page">
    <div class="checkout-page__header container pt-8 md:pt-12">
        <nav class="shop-breadcrumb mb-0" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <a href="{{ route('shop.index') }}">Магазин</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <a href="{{ route('cart.index') }}">Корзина</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current" aria-current="page">Оформление заявки</span>
        </nav>

        <h1 class="shop-title">Оформление заявки</h1>

        <p class="shop-subtitle">Укажите контактные данные для отправки заявки.</p>
    </div>

    <div class="container py-8 md:py-12">
        <div class="checkout-page__content">
            <div class="checkout-grid">
                <div class="checkout-form-wrap">
                    <h2 class="checkout-form-wrap__title">Контактные данные</h2>
                    <form action="{{ route('checkout.store') }}" method="POST" class="checkout-form">
                        @csrf
                        <div class="checkout-form__group">
                            <label for="name">Имя *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required minlength="2" maxlength="255" class="checkout-form__input" placeholder="Как к вам обращаться?" autocomplete="name">
                            @error('name')
                                <div class="checkout-form__error-tooltip" role="alert">
                                    <span class="checkout-form__error-tooltip__icon" aria-hidden="true">!</span>
                                    <span class="checkout-form__error-tooltip__text">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="checkout-form__group">
                            <label for="phone">Телефон *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required maxlength="50" class="checkout-form__input" placeholder="+7 (000) 000-00-00" autocomplete="tel">
                            @error('phone')
                                <div class="checkout-form__error-tooltip" role="alert">
                                    <span class="checkout-form__error-tooltip__icon" aria-hidden="true">!</span>
                                    <span class="checkout-form__error-tooltip__text">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="checkout-form__group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255" class="checkout-form__input" placeholder="example@mail.ru" autocomplete="email">
                            @error('email')
                                <div class="checkout-form__error-tooltip" role="alert">
                                    <span class="checkout-form__error-tooltip__icon" aria-hidden="true">!</span>
                                    <span class="checkout-form__error-tooltip__text">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="checkout-form__group">
                            <label for="comment">Комментарий к заявке</label>
                            <textarea id="comment" name="comment" rows="4" maxlength="2000" class="checkout-form__textarea" placeholder="Необязательно">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="checkout-form__error-tooltip" role="alert">
                                    <span class="checkout-form__error-tooltip__icon" aria-hidden="true">!</span>
                                    <span class="checkout-form__error-tooltip__text">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <x-turnstile-widget />
                        @error('cf-turnstile-response')
                            <div class="checkout-form__error-tooltip" role="alert">
                                <span class="checkout-form__error-tooltip__icon" aria-hidden="true">!</span>
                                <span class="checkout-form__error-tooltip__text">{{ $message }}</span>
                            </div>
                        @enderror
                        <button type="submit" class="shop-filter-btn checkout-form__submit">Отправить заявку</button>
                    </form>
                </div>
                <div class="checkout-summary">
                    <h2 class="checkout-summary__title">Состав заказа</h2>
                    <ul class="checkout-summary-list">
                        @foreach($items as $row)
                            <li class="checkout-summary-list__item">
                                <span class="checkout-item-name">{{ $row['product']->name }}</span>
                                @if($row['variant'])
                                    <span class="checkout-item-variant">{{ $row['variant']->attribute_label }}</span>
                                @endif
                                <span class="checkout-item-gender">Пол: {{ $row['product']->gender === null ? 'Универсальный' : ($row['product']->gender_label ?? '—') }}</span>
                                <span class="checkout-item-qty">{{ $row['quantity'] }} × {{ number_format($row['price'], 0, ',', ' ') }} ₽</span>
                            </li>
                        @endforeach
                    </ul>
                    @if(($discount ?? 0) > 0)
                        <p class="checkout-discount">Промокод {{ $promo->code ?? '' }}: −{{ number_format($discount, 0, ',', ' ') }} ₽</p>
                    @endif
                    <p class="checkout-total"><strong>Итого: {{ number_format($total_final ?? $total, 0, ',', ' ') }} ₽</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if(config('turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <script>
(function() {
    var form = document.querySelector('.checkout-form');
    if (!form) return;

    var nameEl = form.querySelector('[name="name"]');
    var phoneEl = form.querySelector('[name="phone"]');
    var emailEl = form.querySelector('[name="email"]');
    var commentEl = form.querySelector('[name="comment"]');

    function clearValidity(el) {
        if (el) el.setCustomValidity('');
    }

    function validateName() {
        if (!nameEl) return true;
        var v = (nameEl.value || '').trim();
        if (v.length === 0) {
            nameEl.setCustomValidity('Укажите имя.');
            return false;
        }
        if (v.length < 2) {
            nameEl.setCustomValidity('Имя должно содержать не менее 2 символов.');
            return false;
        }
        nameEl.setCustomValidity('');
        return true;
    }

    function validatePhone() {
        if (!phoneEl) return true;
        var v = (phoneEl.value || '').trim();
        if (v.length === 0) {
            phoneEl.setCustomValidity('Укажите телефон.');
            return false;
        }
        var digits = v.replace(/\D/g, '');
        if (digits.length < 10) {
            phoneEl.setCustomValidity('Укажите корректный номер телефона (например, +7 999 123-45-67).');
            return false;
        }
        phoneEl.setCustomValidity('');
        return true;
    }

    function validateEmail() {
        if (!emailEl) return true;
        emailEl.setCustomValidity('');
        if (!emailEl.value || !emailEl.value.trim()) {
            emailEl.setCustomValidity('Укажите email.');
            return false;
        }
        return true;
    }

    function validateTurnstile() {
        var hasWidget = form.querySelector('.cf-turnstile');
        if (!hasWidget) return true;
        var turnstileEl = form.querySelector('[name="cf-turnstile-response"]');
        var token = turnstileEl ? (turnstileEl.value || '').trim() : '';
        if (!token) {
            if (turnstileEl) {
                turnstileEl.setCustomValidity('Пожалуйста, подтвердите, что вы не робот.');
            } else if (emailEl) {
                emailEl.setCustomValidity('Пожалуйста, подтвердите, что вы не робот.');
                emailEl.focus();
                emailEl.reportValidity();
            }
            return false;
        }
        if (turnstileEl) turnstileEl.setCustomValidity('');
        return true;
    }

    [nameEl, phoneEl, emailEl].forEach(function(el) {
        if (!el) return;
        el.addEventListener('input', function() { clearValidity(el); });
        el.addEventListener('change', function() { clearValidity(el); });
    });

    form.addEventListener('submit', function(e) {
        validateName();
        validatePhone();
        validateEmail();
        if (!validateTurnstile()) {
            e.preventDefault();
            form.reportValidity();
            return;
        }
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
            return;
        }

        e.preventDefault();
        var submitBtn = form.querySelector('.checkout-form__submit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-busy', 'true');
        }

        var body = new FormData(form);
        var url = form.getAttribute('action');
        var opts = {
            method: 'POST',
            body: body,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        };

        fetch(url, opts)
            .then(function(res) {
                if (res.status === 422) return res.json().then(function(data) { throw data; });
                if (!res.ok) throw new Error('Ошибка отправки');
                return res.json();
            })
            .then(function(data) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (submitBtn) { submitBtn.disabled = false; submitBtn.removeAttribute('aria-busy'); }
            })
            .catch(function(err) {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.removeAttribute('aria-busy'); }
                if (err && err.errors) {
                    var firstField = null;
                    var firstMsg = null;
                    for (var field in err.errors) {
                        var msg = Array.isArray(err.errors[field]) ? err.errors[field][0] : err.errors[field];
                        var el = form.querySelector('[name="' + field + '"]');
                        if (el) {
                            el.setCustomValidity(msg);
                            if (!firstField) { firstField = el; firstMsg = msg; }
                        }
                    }
                    if (firstField) {
                        firstField.focus();
                        firstField.reportValidity();
                    }
                } else if (err && err.message) {
                    if (nameEl) {
                        nameEl.setCustomValidity(err.message);
                        nameEl.focus();
                        nameEl.reportValidity();
                    }
                }
            });
    });
})();
    </script>
@endpush
