{{-- Футер по макету: логотип + VK/Telegram/RuTube (иконки как в навбаре) + часы | email + телефон | О команде, Результаты --}}
<footer class="footer" aria-label="Подвал сайта">
    {{-- Единый SVG: форма 1100×272 (фиолетовое стекло) + орнамент 272×271 справа внизу --}}
    <div class="footer__shape-wrap">
        <svg class="footer__shape" viewBox="0 0 1100 272" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" aria-hidden="true">
            {{-- Форма футера из макета (скос слева сверху, скругления справа снизу) --}}
            <path d="M34.5 30.5L62.6569 2.34314C64.1571 0.842854 66.192 0 68.3137 0H1092C1096.42 0 1100 3.58172 1100 8V203.615C1100 205.779 1099.12 207.85 1097.57 209.357L1066.5 239.5L1037.36 269.568C1035.85 271.122 1033.78 272 1031.61 272H8C3.58172 272 0 268.418 0 264V68.3137C0 66.192 0.842855 64.1571 2.34315 62.6569L34.5 30.5Z" fill="#8D49EE" fill-opacity="0.6"/>
            {{-- Орнамент (растровое изображение — лабиринт по диагонали), прижат к правому нижнему углу формы --}}
            <image class="footer__ornament-img" href="{{ asset('images/ornaments/footer-ornament.png') }}" x="828" y="0" width="272" height="271" preserveAspectRatio="xMaxYMax meet" opacity="0.9"/>
        </svg>
    </div>
    <div class="footer__content-wrap relative z-[1] flex flex-col justify-center py-12 px-4 pb-10 min-h-[272px] box-border">
        <div class="container footer__container flex-1 flex flex-col justify-between w-full max-w-full m-0 p-0 box-border">
            <div class="footer__grid grid grid-cols-1 md:grid-cols-3 gap-8 items-start text-white m-0">
                <div class="footer__col footer__col--brand max-w-[320px]">
                    <a href="{{ route('home') }}" class="footer__logo inline-block mb-3">
                        <img src="{{ asset('images/logo/logo.png') }}" alt="SPRUT" class="footer__logo-img block h-12 w-[147px] object-contain" width="147" height="48">
                    </a>
                    <div class="footer__social flex gap-1.5 mb-3 w-[78px] h-6 items-center">
                        <a href="{{ $siteContact['vk_url'] ?? 'https://vk.com/urbanrunninggames' }}" target="_blank" rel="noopener" class="footer__social-link footer__social-link--vk" aria-label="VK">
                            <svg class="footer__social-icon" width="24" height="24" aria-hidden="true"><use href="#icon-nav-vk"/></svg>
                        </a>
                        <a href="{{ $siteContact['telegram_url'] ?? 'https://t.me/urbanrunninggames' }}" target="_blank" rel="noopener" class="footer__social-link footer__social-link--tg" aria-label="Telegram">
                            <svg class="footer__social-icon" width="24" height="24" aria-hidden="true"><use href="#icon-nav-telegram"/></svg>
                        </a>
                        <a href="{{ $siteContact['rutube_url'] ?? 'https://rutube.ru' }}" target="_blank" rel="noopener" class="footer__social-link footer__social-link--rutube" aria-label="RuTube">
                            <svg class="footer__social-icon" width="24" height="24" aria-hidden="true"><use href="#icon-nav-rutube"/></svg>
                        </a>
                    </div>
                    <div class="footer__schedule text-base leading-5 text-white mt-0 max-w-[279px]">
                        <p class="m-0 mb-1">{{ $siteContact['schedule_weekdays'] ?? 'Понедельник-пятница — 9:00-18:00' }}</p>
                        <p class="m-0 mb-1">{{ $siteContact['schedule_events'] ?? 'В дни мероприятий — 6:00-0:00' }}</p>
                        <p class="m-0 mb-0">{{ $siteContact['schedule_note'] ?? 'Отвечаем в Telegram' }}</p>
                    </div>
                </div>
                <div class="footer__col footer__col--contacts flex flex-col gap-1">
                    <p class="footer__contact-item m-0 text-lg font-bold text-white"><a href="mailto:{{ e($siteContact['email'] ?? 'main@sprut.run') }}" class="text-white no-underline hover:underline">{{ e($siteContact['email'] ?? 'main@sprut.run') }}</a></p>
                    <p class="footer__contact-item m-0 text-lg font-bold text-white"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteContact['phone'] ?? '') }}" class="text-white no-underline hover:underline">{{ e($siteContact['phone'] ?? '+7(917)806-09-95') }}</a></p>
                </div>
                <div class="footer__col footer__col--links">
                    <ul class="footer__links list-none m-0 p-0">
                        <li><a href="{{ route('about') }}">О команде</a></li>
                        <li><a href="{{ route('rating') }}">Результаты</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
