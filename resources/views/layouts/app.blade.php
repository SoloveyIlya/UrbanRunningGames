<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Urban Running Games')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Nunito:wght@700&display=swap" rel="stylesheet">
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
    <svg width="0" height="0" aria-hidden="true">
        <defs>
            <clipPath id="navbar-clip" clipPathUnits="objectBoundingBox">
                <path d="M0 0.227 C0 0.102 0.0065 0 0.0145 0 L0.985 0 C0.996 0 1.003 0.191 0.997 0.343 L0.977 0.888 C0.974 0.957 0.969 1 0.964 1 L0.0145 1 C0.0065 1 0 0.898 0 0.773 L0 0.227 Z"/>
            </clipPath>
            <!-- Форма секции home-stats (1340×636 → objectBoundingBox) — текст обрезается по границе изображения -->
            <clipPath id="home-stats-section-clip" clipPathUnits="objectBoundingBox">
                <path d="M0 0.196 C0 0.189 0.00113 0.182 0.00322 0.176 L0.065 0.012 C0.068 0.004 0.072 0 0.077 0 H0.985 C0.993 0 1 0.014 1 0.031 V0.83 C1 0.838 0.998 0.846 0.995 0.852 L0.93 0.991 C0.927 0.997 0.923 1 0.919 1 H0.015 C0.0067 1 0 0.986 0 0.969 V0.196 Z"/>
            </clipPath>
            <!-- Карточка «Смотреть остальные гонки» — скос правого нижнего угла (618×327 → objectBoundingBox) -->
            <clipPath id="race-card-all-clip" clipPathUnits="objectBoundingBox">
                <path d="M0.0065 0.061 C0.0065 0.027 0.021 0 0.039 0 H0.961 C0.979 0 0.994 0.061 0.994 0.061 V0.645 C0.994 0.661 0.99 0.676 0.984 0.688 L0.841 0.958 C0.835 0.969 0.827 0.976 0.818 0.976 H0.039 C0.0065 0.976 0.0065 0.914 0.0065 0.061 Z"/>
            </clipPath>
        </defs>
    </svg>
    <nav class="navbar">
        <div class="container">
            <div class="navbar__inner">
                <ul class="navbar__left">
                    <li><a href="{{ route('shop.index') }}">Магазин</a></li>
                    <li><a href="{{ route('about') }}">Команда</a></li>
                    <li><a href="{{ route('partners') }}">Партнёры</a></li>
                    <li><a href="{{ route('contact') }}">Контакты</a></li>
                </ul>
                <a href="{{ route('home') }}" class="navbar__logo">
                    <img src="{{ asset('images/logo/sprut.svg') }}" alt="SPRUT" class="navbar__logo-img">
                </a>
                <div class="navbar__right">
                    <div class="navbar__links">
                        <a href="{{ route('events.index') }}" class="navbar__link">Гонки</a>
                        <span class="navbar__line"></span>
                        <a href="{{ route('rating') }}" class="navbar__link">Рейтинг</a>
                        <span class="navbar__line"></span>
                        <a href="{{ route('gallery.index') }}" class="navbar__link">Фото</a>
                    </div>
                    <div class="navbar__social">
                        <a href="{{ $siteContact['vk_url'] ?? 'https://vk.com/urbanrunninggames' }}" target="_blank" rel="noopener" class="navbar__social-link" aria-label="VK">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.4 3.4L3.04475 3.0481L3.04896 3.04395L3.4 3.4ZM3.4 20.6L3.0481 20.9552L3.04395 20.951L3.4 20.6ZM20.6 20.6L20.9553 20.9519L20.9511 20.9561L20.6 20.6ZM20.6 3.4L20.9519 3.04475L20.9561 3.04896L20.6 3.4ZM5.37333 8.08667L4.87343 8.09692C4.87068 7.96256 4.92212 7.83275 5.01617 7.73676C5.11022 7.64076 5.23894 7.58667 5.37333 7.58667V8.08667ZM12.9067 16.4067H13.4067C13.4067 16.6828 13.1828 16.9067 12.9067 16.9067V16.4067ZM12.9067 13.4333H12.4067C12.4067 13.2921 12.4664 13.1575 12.5711 13.0627C12.6758 12.9679 12.8157 12.9218 12.9563 12.9358L12.9067 13.4333ZM16.3534 16.4067V16.9067C16.136 16.9067 15.9436 16.7663 15.8773 16.5593L16.3534 16.4067ZM18.72 16.4067L19.2027 16.2762C19.2433 16.4264 19.2117 16.587 19.1171 16.7106C19.0224 16.8342 18.8757 16.9067 18.72 16.9067V16.4067ZM15.3 12.2333L15.0772 12.681C14.9143 12.5998 14.8082 12.4365 14.8005 12.2546C14.7927 12.0727 14.8845 11.901 15.04 11.8063L15.3 12.2333ZM18.22 8.08V7.58C18.3702 7.58 18.5124 7.6475 18.6074 7.76385C18.7024 7.88019 18.74 8.03306 18.7099 8.18019L18.22 8.08ZM16.0734 8.08L15.5922 7.94418C15.653 7.72876 15.8495 7.58 16.0734 7.58V8.08ZM12.9067 11.3733L12.9597 11.8705C12.8187 11.8856 12.6779 11.84 12.5724 11.7452C12.4669 11.6503 12.4067 11.5152 12.4067 11.3733H12.9067ZM12.9067 8.08667V7.58667C13.1828 7.58667 13.4067 7.81053 13.4067 8.08667H12.9067ZM10.7534 8.08667H10.2534C10.2534 7.81053 10.4772 7.58667 10.7534 7.58667V8.08667ZM10.7534 13.8467H11.2534C11.2534 14.0006 11.1824 14.146 11.0611 14.2408C10.9397 14.3355 10.7815 14.3691 10.6321 14.3317L10.7534 13.8467ZM7.66666 8.08667V7.58667C7.93906 7.58667 8.16133 7.80471 8.16657 8.07705L7.66666 8.08667ZM1.5 11.6C1.5 9.35073 1.49895 7.61455 1.67942 6.26617C1.86181 4.90342 2.23767 3.86287 3.04475 3.0481L3.75522 3.75187C3.16231 4.35043 2.83819 5.14658 2.67058 6.39883C2.50105 7.66545 2.5 9.3226 2.5 11.6H1.5ZM1.5 12.4V11.6H2.5V12.4H1.5ZM3.04395 20.951C2.23765 20.1332 1.86182 19.0917 1.67942 17.7288C1.49895 16.3805 1.5 14.646 1.5 12.4H2.5C2.5 14.674 2.50105 16.3295 2.67058 17.5962C2.83817 18.8483 3.16235 19.6468 3.75605 20.249L3.04395 20.951ZM11.6 22.5C9.35073 22.5 7.61455 22.5011 6.26617 22.3206C4.90341 22.1382 3.86287 21.7623 3.0481 20.9552L3.75187 20.2448C4.35043 20.8377 5.14658 21.1618 6.39883 21.3294C7.66545 21.4989 9.3226 21.5 11.6 21.5V22.5ZM12.4 22.5H11.6V21.5H12.4V22.5ZM20.9511 20.9561C20.1333 21.7624 19.0917 22.1382 17.7289 22.3206C16.3805 22.5011 14.646 22.5 12.4 22.5V21.5C14.6741 21.5 16.3296 21.4989 17.5962 21.3294C18.8484 21.1618 19.6468 20.8376 20.249 20.2439L20.9511 20.9561ZM22.5 12.4C22.5 14.6493 22.5011 16.3855 22.3206 17.7338C22.1382 19.0966 21.7624 20.1371 20.9553 20.9519L20.2448 20.2481C20.8377 19.6496 21.1619 18.8534 21.3295 17.6012C21.499 16.3345 21.5 14.6774 21.5 12.4H22.5ZM22.5 11.6V12.4H21.5V11.6H22.5ZM20.9561 3.04896C21.7624 3.86678 22.1382 4.90834 22.3206 6.27117C22.5011 7.61954 22.5 9.35404 22.5 11.6H21.5C21.5 9.32596 21.499 7.67046 21.3295 6.40383C21.1619 5.15166 20.8377 4.35322 20.244 3.75104L20.9561 3.04896ZM12.4 1.5C14.6493 1.5 16.3855 1.49895 17.7339 1.67942C19.0966 1.86181 20.1371 2.23766 20.9519 3.04475L20.2482 3.75522C19.6496 3.16231 18.8535 2.83819 17.6012 2.67058C16.3346 2.50105 14.6774 2.5 12.4 2.5V1.5ZM11.6 1.5H12.4V2.5H11.6V1.5ZM3.04896 3.04395C3.86678 2.23765 4.90834 1.86183 6.27117 1.67942C7.61954 1.49895 9.35404 1.5 11.6 1.5V2.5C9.32596 2.5 7.67045 2.50105 6.40383 2.67058C5.15166 2.83817 4.35322 3.16235 3.75104 3.75605L3.04896 3.04395ZM12.64 16.9067C10.2378 16.9067 8.30052 16.0798 6.95803 14.5232C5.62549 12.9782 4.92831 10.7719 4.87343 8.09692L5.87323 8.07641C5.92502 10.6014 6.58116 12.5551 7.71529 13.8701C8.83947 15.1735 10.4823 15.9067 12.64 15.9067V16.9067ZM12.6467 16.9067H12.64V15.9067H12.6467V16.9067ZM12.9067 16.9067H12.6467V15.9067H12.9067V16.9067ZM13.4067 13.4333V16.4067H12.4067V13.4333H13.4067ZM15.8773 16.5593C15.4193 15.1312 14.2956 14.0741 12.8571 13.9309L12.9563 12.9358C14.8644 13.1259 16.2741 14.5221 16.8295 16.254L15.8773 16.5593ZM18.72 16.9067H16.3534V15.9067H18.72V16.9067ZM17.8442 13.6364C18.4785 14.4104 18.9415 15.3101 19.2027 16.2762L18.2374 16.5372C18.0131 15.7076 17.6155 14.9349 17.0707 14.2703L17.8442 13.6364ZM15.5228 11.7857C16.4187 12.2316 17.2099 12.8624 17.8442 13.6364L17.0707 14.2703C16.526 13.6056 15.8466 13.0639 15.0772 12.681L15.5228 11.7857ZM17.602 10.7486C17.06 11.5195 16.3649 12.1703 15.5601 12.6604L15.04 11.8063C15.7274 11.3877 16.321 10.8318 16.7839 10.1735L17.602 10.7486ZM18.7099 8.18019C18.5211 9.10338 18.1439 9.97773 17.602 10.7486L16.7839 10.1735C17.2468 9.51507 17.5689 8.76831 17.7302 7.97982L18.7099 8.18019ZM16.0734 7.58H18.22V8.58H16.0734V7.58ZM12.8537 10.8762C13.3502 10.8232 13.9227 10.4996 14.445 9.94981C14.9607 9.40692 15.3818 8.68939 15.5922 7.94418L16.5546 8.21582C16.2982 9.12394 15.7927 9.98308 15.1701 10.6385C14.554 11.287 13.7766 11.7834 12.9597 11.8705L12.8537 10.8762ZM13.4067 8.08667V11.3733H12.4067V8.08667H13.4067ZM10.7534 7.58667H12.9067V8.58667H10.7534V7.58667ZM10.2534 13.8467V8.08667H11.2534V13.8467H10.2534ZM8.16657 8.07705C8.23725 11.7526 9.83541 13.1018 10.8746 13.3616L10.6321 14.3317C9.00459 13.9249 7.24274 12.0474 7.16676 8.09628L8.16657 8.07705ZM5.37333 7.58667H7.66666V8.58667H5.37333V7.58667Z"/></svg>
                        </a>
                        <a href="{{ $siteContact['telegram_url'] ?? 'https://t.me/urbanrunninggames' }}" target="_blank" rel="noopener" class="navbar__social-link navbar__social-link--outline" aria-label="Telegram">
                            <svg viewBox="36 0 22 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path fill-rule="evenodd" clip-rule="evenodd" d="M55.9971 12C55.9971 17.5228 51.5199 22 45.9971 22C40.4742 22 35.9971 17.5228 35.9971 12C35.9971 6.47715 40.4742 2 45.9971 2C51.5199 2 55.9971 6.47715 55.9971 12ZM46.3554 9.38244C45.3828 9.787 43.4388 10.6243 40.5236 11.8944C40.0503 12.0827 39.8023 12.2669 39.7797 12.4469C39.7416 12.7513 40.1227 12.8711 40.6416 13.0343C40.7122 13.0565 40.7854 13.0795 40.8603 13.1038C41.3709 13.2698 42.0577 13.464 42.4148 13.4717C42.7387 13.4787 43.1002 13.3452 43.4993 13.0711C46.223 11.2325 47.6291 10.3032 47.7173 10.2831C47.7796 10.269 47.8659 10.2512 47.9244 10.3032C47.9829 10.3552 47.9771 10.4536 47.9709 10.48C47.9332 10.641 46.4372 12.0318 45.663 12.7515C45.4217 12.9759 45.2505 13.135 45.2155 13.1714C45.1371 13.2528 45.0572 13.3298 44.9804 13.4038C44.5061 13.8611 44.1503 14.204 45.0001 14.764C45.4084 15.0331 45.7352 15.2556 46.0612 15.4776C46.4172 15.7201 46.7723 15.9619 47.2318 16.2631C47.3488 16.3398 47.4606 16.4195 47.5695 16.4971C47.9838 16.7925 48.356 17.0579 48.8159 17.0155C49.0831 16.991 49.3591 16.7397 49.4993 15.9903C49.8306 14.2193 50.4818 10.382 50.6323 8.80081C50.6455 8.66228 50.6289 8.48498 50.6155 8.40715C50.6022 8.32932 50.5744 8.21842 50.4732 8.13633C50.3534 8.03911 50.1684 8.01861 50.0857 8.02C49.7096 8.0267 49.1325 8.22735 46.3554 9.38244Z"/></svg>
                        </a>
                    </div>
                </div>
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Меню">☰</button>
            </div>
        </div>
    </nav>
    <ul class="nav-menu nav-menu--mobile" id="navMenu" aria-hidden="true">
        <li><a href="{{ route('shop.index') }}">Магазин</a></li>
        <li><a href="{{ route('about') }}">Команда</a></li>
        <li><a href="{{ route('partners') }}">Партнёры</a></li>
        <li><a href="{{ route('contact') }}">Контакты</a></li>
        <li><a href="{{ route('events.index') }}">Гонки</a></li>
        <li><a href="{{ route('rating') }}">Рейтинг</a></li>
        <li><a href="{{ route('gallery.index') }}">Фото</a></li>
        <li><a href="{{ route('cart.index') }}">Корзина @if(\App\Http\Controllers\CartController::getCount() > 0)({{ \App\Http\Controllers\CartController::getCount() }})@endif</a></li>
    </ul>

    <main class="main-after-header">
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

    @include('partials.footer.footer')

    <script>
        // Мобильное меню
        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            document.getElementById('navMenu')?.classList.toggle('active');
        });
        // Аккордеон секции «Информация»
        document.querySelectorAll('[data-accordion-trigger]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var item = this.closest('[data-accordion-item]');
                var body = document.getElementById(this.getAttribute('aria-controls'));
                var isOpen = item.hasAttribute('data-open');
                if (isOpen) {
                    item.removeAttribute('data-open');
                    this.setAttribute('aria-expanded', 'false');
                    if (body) body.hidden = true;
                } else {
                    item.setAttribute('data-open', '');
                    this.setAttribute('aria-expanded', 'true');
                    if (body) body.hidden = false;
                }
            });
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
