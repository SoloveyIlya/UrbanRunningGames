<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Urban Running Games')</title>
    @php
        $useVite = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
    @endphp
    @if($useVite)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        @php
            $cssPath = resource_path('css/app.css');
            if (file_exists($cssPath)) {
                echo '<style>' . file_get_contents($cssPath) . '</style>';
            }
        @endphp
    @endif
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="logo">Urban Running Games</a>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="{{ route('home') }}">Главная</a></li>
                <li><a href="{{ route('about') }}">О команде</a></li>
                <li><a href="{{ route('rules') }}">Правила</a></li>
                <li><a href="{{ route('events.index') }}">События</a></li>
                <li><a href="{{ route('events.archive') }}">Архив</a></li>
                <li><a href="{{ route('gallery.index') }}">Фотогалерея</a></li>
                <li><a href="{{ route('shop.index') }}">Магазин</a></li>
                <li><a href="{{ route('cart.index') }}" class="nav-cart-link">Корзина @if(\App\Http\Controllers\CartController::getCount() > 0)<span class="cart-count">({{ \App\Http\Controllers\CartController::getCount() }})</span>@endif</a></li>
                <li><a href="{{ route('partners') }}">Партнёры</a></li>
                <li><a href="{{ route('rating') }}">Рейтинг</a></li>
                <li><a href="{{ route('contact') }}">Контакты</a></li>
            </ul>
        </div>
    </nav>

    <main>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Urban Running Games</h3>
                    <p>Командные забеги-игры</p>
                </div>
                <div class="footer-section">
                    <h4>Навигация</h4>
                    <ul>
                        <li><a href="{{ route('about') }}">О команде</a></li>
                        <li><a href="{{ route('events.index') }}">События</a></li>
                        <li><a href="{{ route('gallery.index') }}">Фотогалерея</a></li>
                        <li><a href="{{ route('shop.index') }}">Магазин</a></li>
                        <li><a href="{{ route('partners') }}">Партнёры</a></li>
                        <li><a href="{{ route('contact') }}">Контакты</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Юридическая информация</h4>
                    <ul>
                        <li><a href="{{ route('legal.privacy') }}">Политика конфиденциальности</a></li>
                        <li><a href="{{ route('legal.consent') }}">Согласие на обработку ПДн</a></li>
                        <li><a href="{{ route('legal.terms') }}">Условия продажи мерча</a></li>
                        <li><a href="{{ route('legal.returns') }}">Правила возвратов</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Urban Running Games. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script>
        // Мобильное меню
        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            document.getElementById('navMenu')?.classList.toggle('active');
        });
        // При 404: сначала пробуем data-full (полноразмерное), затем плейсхолдер
        document.addEventListener('error', function(e) {
            if (e.target && e.target.tagName !== 'IMG') return;
            var img = e.target;
            if (img.dataset.fallbackDone) {
                img.src = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect fill="#eee" width="400" height="400"/><text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="14" text-anchor="middle" dy=".3em">Фото недоступно</text></svg>');
                img.classList.add('img-placeholder');
                return;
            }
            var fullUrl = img.getAttribute('data-full');
            if (fullUrl) {
                fullUrl = fullUrl.indexOf('http') === 0 ? fullUrl : (window.location.origin + (fullUrl.charAt(0) === '/' ? '' : '/') + fullUrl);
                img.src = fullUrl;
                img.removeAttribute('data-full');
                img.dataset.fallbackDone = '1';
                return;
            }
            img.src = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect fill="#eee" width="400" height="400"/><text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="14" text-anchor="middle" dy=".3em">Фото недоступно</text></svg>');
            img.classList.add('img-placeholder');
            console.warn('[Storage 404]', img.alt || img.getAttribute('src'), window.location.href);
        }, true);
    </script>
    @stack('scripts')
</body>
</html>
