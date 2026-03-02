<?php

namespace App\Filament\Pages;

use App\Models\HeroVideo;
use App\Models\MediaAsset;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Services\HeroVideoUploadService;
use App\Services\ImageOptimizationService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SiteContentPage extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Контент сайта';
    protected static ?string $title = 'Контент сайта';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'site-content';
    protected static string $view = 'filament.pages.site-content-page';

    public ?array $data = [];

    public function mount(): void
    {
        $pages = SitePage::query()
            ->whereIn('slug', SitePage::slugs())
            ->get()
            ->keyBy('slug');

        $data = ['pages' => []];
        foreach (SitePage::slugs() as $slug) {
            $page = $pages->get($slug);
            $data['pages'][$slug] = [
                'title' => $page?->title ?? $this->defaultTitle($slug),
                'content' => $page?->content ?? '',
            ];
        }

        $data['contact'] = [
            'vk_url' => SiteSetting::get(SiteSetting::KEY_VK_URL, 'https://vk.com/urbanrunninggames'),
            'telegram_url' => SiteSetting::get(SiteSetting::KEY_TELEGRAM_URL, 'https://t.me/urbanrunninggames'),
            'rutube_url' => SiteSetting::get(SiteSetting::KEY_RUTUBE_URL, '#'),
            'email' => SiteSetting::get(SiteSetting::KEY_EMAIL, 'main@sprut.run'),
            'phone' => SiteSetting::get(SiteSetting::KEY_PHONE, '+79178060995'),
            'schedule_weekdays' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_WEEKDAYS, 'Понедельник–пятница — 9:00–18:00'),
            'schedule_events' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_EVENTS, 'В дни мероприятий — 6:00–0:00'),
            'schedule_note' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_NOTE, 'Отвечаем в Telegram'),
            'company_name' => SiteSetting::get(SiteSetting::KEY_COMPANY_NAME, 'ООО «СПРУТ»'),
            'inn' => SiteSetting::get(SiteSetting::KEY_INN, '9731015256'),
            'kpp' => SiteSetting::get(SiteSetting::KEY_KPP, '773101001'),
            'ogrn' => SiteSetting::get(SiteSetting::KEY_OGRN, '1187746928588'),
        ];

        $data['home_stats'] = [];
        for ($i = 1; $i <= 4; $i++) {
            $data['home_stats'][] = [
                'number' => SiteSetting::get("home_stat_{$i}_number", $this->defaultHomeStatNumber($i)),
                'label' => SiteSetting::get("home_stat_{$i}_label", $this->defaultHomeStatLabel($i)),
                'desc' => SiteSetting::get("home_stat_{$i}_desc", $this->defaultHomeStatDesc($i)),
            ];
        }

        $heroMain = HeroVideo::where('page', HeroVideo::PAGE_MAIN)->first();
        $data['hero_main'] = [
            'is_enabled' => $heroMain?->is_enabled ?? true,
            'video_upload' => null,
            'poster_upload' => null,
            'title' => $heroMain?->title,
            'button_text' => $heroMain?->button_text,
            'button_url' => $heroMain?->button_url,
        ];

        $heroEvents = HeroVideo::where('page', HeroVideo::PAGE_EVENTS)->first();
        $data['hero_events'] = [
            'is_enabled' => $heroEvents?->is_enabled ?? true,
            'video_upload' => null,
            'poster_upload' => null,
            'title' => $heroEvents?->title,
            'button_text' => $heroEvents?->button_text,
            'button_url' => $heroEvents?->button_url,
        ];

        $data['hero_shop'] = [
            'overlay_opacity' => SiteSetting::get(SiteSetting::KEY_SHOP_HERO_OVERLAY_OPACITY, '0.5'),
            'slide_1_upload' => null,
            'slide_2_upload' => null,
            'slide_3_upload' => null,
        ];

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Hero-контент')
                            ->icon('heroicon-o-film')
                            ->schema($this->heroContentSchema()),
                        Forms\Components\Tabs\Tab::make('Контент по страницам')
                            ->icon('heroicon-o-document-text')
                            ->schema($this->pagesContentSchema()),
                        Forms\Components\Tabs\Tab::make('Контакты и ссылки')
                            ->icon('heroicon-o-phone')
                            ->schema($this->contactSchema()),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function heroContentSchema(): array
    {
        $heroMain = HeroVideo::with(['videoMedia', 'posterMedia'])->where('page', HeroVideo::PAGE_MAIN)->first();
        $heroEvents = HeroVideo::with(['videoMedia', 'posterMedia'])->where('page', HeroVideo::PAGE_EVENTS)->first();
        $slide1Media = $this->getShopSlideMedia(1);
        $slide2Media = $this->getShopSlideMedia(2);
        $slide3Media = $this->getShopSlideMedia(3);

        return [
            Forms\Components\Section::make('Hero главной страницы')
                ->description('Видео и постер для hero-блока на главной. mp4 (H.264), автовоспроизведение без звука, зацикливание.')
                ->schema([
                    Forms\Components\Toggle::make('hero_main.is_enabled')
                        ->label('Включено')
                        ->default(true)
                        ->helperText('Показывать видео в hero-блоке'),
                    Forms\Components\FileUpload::make('hero_main.video_upload')
                        ->label('Видео (mp4, желательно webm)')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])
                        ->maxSize(100 * 1024 * 1024)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false)
                        ->helperText('Загрузка / замена. Формат mp4 (H.264) обязателен.'),
                    Forms\Components\Placeholder::make('hero_main.current_video')
                        ->label('Текущее видео')
                        ->content($heroMain && $heroMain->videoMedia
                            ? new \Illuminate\Support\HtmlString('<a href="' . e($heroMain->video_url) . '" target="_blank" rel="noopener">' . e($heroMain->videoMedia->original_name ?? 'Видео') . '</a>')
                            : '—'),
                    Forms\Components\FileUpload::make('hero_main.poster_upload')
                        ->label('Постер (fallback при невозможности autoplay)')
                        ->image()
                        ->imagePreviewHeight(120)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false),
                    Forms\Components\Placeholder::make('hero_main.current_poster')
                        ->label('Текущий постер')
                        ->content($heroMain && $heroMain->posterMedia?->url
                            ? new \Illuminate\Support\HtmlString('<img src="' . e($heroMain->poster_url) . '" alt="" style="max-height:120px;">')
                            : '—'),
                    Forms\Components\TextInput::make('hero_main.title')
                        ->label('Заголовок')
                        ->maxLength(255)
                        ->nullable()
                        ->placeholder('Например: Urban Running Games'),
                    Forms\Components\TextInput::make('hero_main.button_text')
                        ->label('Текст кнопки')
                        ->maxLength(64)
                        ->nullable()
                        ->placeholder('Предстоящие события'),
                    Forms\Components\TextInput::make('hero_main.button_url')
                        ->label('URL кнопки')
                        ->maxLength(512)
                        ->nullable()
                        ->placeholder('/events')
                        ->helperText('Относительный путь (например /events) или полный URL'),
                ])
                ->columns(1),
            Forms\Components\Section::make('Hero страницы «События»')
                ->description('Видео и постер для hero-блока на странице событий.')
                ->schema([
                    Forms\Components\Toggle::make('hero_events.is_enabled')
                        ->label('Включено')
                        ->default(true)
                        ->helperText('Показывать видео в hero-блоке'),
                    Forms\Components\FileUpload::make('hero_events.video_upload')
                        ->label('Видео (mp4, желательно webm)')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])
                        ->maxSize(100 * 1024 * 1024)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false),
                    Forms\Components\Placeholder::make('hero_events.current_video')
                        ->label('Текущее видео')
                        ->content($heroEvents && $heroEvents->videoMedia
                            ? new \Illuminate\Support\HtmlString('<a href="' . e($heroEvents->video_url) . '" target="_blank" rel="noopener">' . e($heroEvents->videoMedia->original_name ?? 'Видео') . '</a>')
                            : '—'),
                    Forms\Components\FileUpload::make('hero_events.poster_upload')
                        ->label('Постер')
                        ->image()
                        ->imagePreviewHeight(120)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false),
                    Forms\Components\Placeholder::make('hero_events.current_poster')
                        ->label('Текущий постер')
                        ->content($heroEvents && $heroEvents->posterMedia?->url
                            ? new \Illuminate\Support\HtmlString('<img src="' . e($heroEvents->poster_url) . '" alt="" style="max-height:120px;">')
                            : '—'),
                    Forms\Components\TextInput::make('hero_events.title')
                        ->label('Заголовок')
                        ->maxLength(255)
                        ->nullable(),
                    Forms\Components\TextInput::make('hero_events.button_text')
                        ->label('Текст кнопки')
                        ->maxLength(64)
                        ->nullable(),
                    Forms\Components\TextInput::make('hero_events.button_url')
                        ->label('URL кнопки')
                        ->maxLength(512)
                        ->nullable(),
                ])
                ->columns(1),
            Forms\Components\Section::make('Hero страницы магазина (слайдер и оверлей)')
                ->description('Слайдер сверху и затемняющий оверлей на странице магазина. Загрузка изображений как в альбомах.')
                ->schema([
                    Forms\Components\TextInput::make('hero_shop.overlay_opacity')
                        ->label('Непрозрачность оверлея (0–1)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(1)
                        ->step(0.05)
                        ->default(0.5)
                        ->helperText('Чем больше — тем темнее оверлей поверх слайдов (например 0.5).'),
                    Forms\Components\FileUpload::make('hero_shop.slide_1_upload')
                        ->label('Слайд 1 — фоновое изображение')
                        ->image()
                        ->maxSize(50 * 1024)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false)
                        ->helperText('Загрузите изображение — как обложка в альбоме. Оставьте пустым для градиента по умолчанию.'),
                    Forms\Components\Placeholder::make('hero_shop.current_slide_1')
                        ->label('Текущий слайд 1')
                        ->content($slide1Media
                            ? new \Illuminate\Support\HtmlString('<img src="' . e($slide1Media->thumbnail_url ?? $slide1Media->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                            : '—'),
                    Forms\Components\FileUpload::make('hero_shop.slide_2_upload')
                        ->label('Слайд 2 — фоновое изображение')
                        ->image()
                        ->maxSize(50 * 1024)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false),
                    Forms\Components\Placeholder::make('hero_shop.current_slide_2')
                        ->label('Текущий слайд 2')
                        ->content($slide2Media
                            ? new \Illuminate\Support\HtmlString('<img src="' . e($slide2Media->thumbnail_url ?? $slide2Media->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                            : '—'),
                    Forms\Components\FileUpload::make('hero_shop.slide_3_upload')
                        ->label('Слайд 3 — фоновое изображение')
                        ->image()
                        ->maxSize(50 * 1024)
                        ->disk('local')
                        ->directory('livewire-tmp')
                        ->visibility('private')
                        ->nullable()
                        ->storeFiles(false),
                    Forms\Components\Placeholder::make('hero_shop.current_slide_3')
                        ->label('Текущий слайд 3')
                        ->content($slide3Media
                            ? new \Illuminate\Support\HtmlString('<img src="' . e($slide3Media->thumbnail_url ?? $slide3Media->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                            : '—'),
                ])
                ->columns(1),
        ];
    }

    protected function getShopSlideMedia(int $index): ?MediaAsset
    {
        $key = match ($index) {
            1 => SiteSetting::KEY_SHOP_HERO_SLIDE_1_MEDIA_ID,
            2 => SiteSetting::KEY_SHOP_HERO_SLIDE_2_MEDIA_ID,
            3 => SiteSetting::KEY_SHOP_HERO_SLIDE_3_MEDIA_ID,
            default => null,
        };
        if (!$key) {
            return null;
        }
        $id = SiteSetting::get($key);
        return $id ? MediaAsset::find($id) : null;
    }

    protected function pagesContentSchema(): array
    {
        $sections = [
            'Главная страница' => [
                Forms\Components\Section::make('Блок статистики (цифры на главной)')
                    ->schema([
                        Forms\Components\Repeater::make('home_stats')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label('Число')
                                    ->maxLength(32)
                                    ->required(),
                                Forms\Components\TextInput::make('label')
                                    ->label('Подпись')
                                    ->maxLength(255)
                                    ->required(),
                                Forms\Components\TextInput::make('desc')
                                    ->label('Описание')
                                    ->maxLength(255)
                                    ->nullable(),
                            ])
                            ->defaultItems(4)
                            ->minItems(4)
                            ->maxItems(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ],
            'О команде' => [
                Forms\Components\Section::make(SitePage::SLUG_ABOUT)
                    ->description('Страница «О команде организатора»')
                    ->schema([
                        Forms\Components\TextInput::make('pages.about.title')
                            ->label('Заголовок')
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('pages.about.content')
                            ->label('Текст (HTML)')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ],
            'Правила' => [
                Forms\Components\Section::make(SitePage::SLUG_RULES)
                    ->description('Правила забега')
                    ->schema([
                        Forms\Components\TextInput::make('pages.rules.title')
                            ->label('Заголовок')
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('pages.rules.content')
                            ->label('Текст (HTML)')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ],
            'Юридические страницы' => [
                Forms\Components\Section::make('Политика конфиденциальности')
                    ->schema([
                        Forms\Components\TextInput::make('pages.privacy.title')->label('Заголовок')->maxLength(255),
                        Forms\Components\RichEditor::make('pages.privacy.content')->label('Текст')->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])->columnSpanFull(),
                    ])->collapsible(),
                Forms\Components\Section::make('Условия продажи мерча')
                    ->schema([
                        Forms\Components\TextInput::make('pages.terms.title')->label('Заголовок')->maxLength(255),
                        Forms\Components\RichEditor::make('pages.terms.content')->label('Текст')->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])->columnSpanFull(),
                    ])->collapsible(),
                Forms\Components\Section::make('Согласие на обработку данных')
                    ->schema([
                        Forms\Components\TextInput::make('pages.consent.title')->label('Заголовок')->maxLength(255),
                        Forms\Components\RichEditor::make('pages.consent.content')->label('Текст')->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])->columnSpanFull(),
                    ])->collapsible(),
                Forms\Components\Section::make('Возврат и обмен')
                    ->schema([
                        Forms\Components\TextInput::make('pages.returns.title')->label('Заголовок')->maxLength(255),
                        Forms\Components\RichEditor::make('pages.returns.content')->label('Текст')->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])->columnSpanFull(),
                    ])->collapsible(),
            ],
        ];

        $schema = [];
        foreach ($sections as $group => $components) {
            foreach ($components as $component) {
                $schema[] = $component;
            }
        }
        return $schema;
    }

    protected function contactSchema(): array
    {
        return [
            Forms\Components\Section::make('Ссылки на соцсети и мессенджеры')
                ->schema([
                    Forms\Components\TextInput::make('contact.vk_url')
                        ->label('VK (URL)')
                        ->url()
                        ->maxLength(512),
                    Forms\Components\TextInput::make('contact.telegram_url')
                        ->label('Telegram (URL)')
                        ->url()
                        ->maxLength(512),
                    Forms\Components\TextInput::make('contact.rutube_url')
                        ->label('RuTube (URL)')
                        ->url()
                        ->maxLength(512),
                ])
                ->columns(1),
            Forms\Components\Section::make('Контактные данные')
                ->schema([
                    Forms\Components\TextInput::make('contact.email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('contact.phone')
                        ->label('Телефон')
                        ->tel()
                        ->maxLength(64),
                ])
                ->columns(1),
            Forms\Components\Section::make('Режим работы (текст в футере)')
                ->schema([
                    Forms\Components\TextInput::make('contact.schedule_weekdays')
                        ->label('Будни')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('contact.schedule_events')
                        ->label('В дни мероприятий')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('contact.schedule_note')
                        ->label('Доп. строка (например: Отвечаем в Telegram)')
                        ->maxLength(255),
                ])
                ->columns(1),
            Forms\Components\Section::make('Реквизиты компании (футер)')
                ->schema([
                    Forms\Components\TextInput::make('contact.company_name')
                        ->label('Название (например: ООО «СПРУТ»)')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('contact.inn')
                        ->label('ИНН')
                        ->maxLength(32),
                    Forms\Components\TextInput::make('contact.kpp')
                        ->label('КПП')
                        ->maxLength(32),
                    Forms\Components\TextInput::make('contact.ogrn')
                        ->label('ОГРН')
                        ->maxLength(32),
                ])
                ->columns(1),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $heroMainData = $data['hero_main'] ?? null;
        if (is_array($heroMainData)) {
            $heroMain = HeroVideo::firstOrCreate(
                ['page' => HeroVideo::PAGE_MAIN],
                ['is_enabled' => true]
            );
            $heroMain->update([
                'is_enabled' => (bool) ($heroMainData['is_enabled'] ?? true),
                'title' => $heroMainData['title'] ?? null,
                'button_text' => $heroMainData['button_text'] ?? null,
                'button_url' => $heroMainData['button_url'] ?? null,
            ]);
            $service = app(HeroVideoUploadService::class);
            $adminId = auth()->id();

            $video = $heroMainData['video_upload'] ?? null;
            if (is_array($video)) {
                $video = array_values($video)[0] ?? null;
            }
            if ($video instanceof TemporaryUploadedFile && $video->exists()) {
                try {
                    $asset = $service->storeVideo($video, $adminId);
                    $heroMain->update(['video_media_id' => $asset->id]);
                } finally {
                    $video->delete();
                }
            } elseif (is_string($video) && $video !== '') {
                $fullPath = Storage::disk('local')->path($video);
                if (is_file($fullPath)) {
                    try {
                        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                        $asset = $service->storeVideo($file, $adminId);
                        $heroMain->update(['video_media_id' => $asset->id]);
                    } finally {
                        @unlink($fullPath);
                    }
                }
            }

            $poster = $heroMainData['poster_upload'] ?? null;
            if (is_array($poster)) {
                $poster = array_values($poster)[0] ?? null;
            }
            if ($poster instanceof TemporaryUploadedFile && $poster->exists()) {
                try {
                    $asset = $service->storePoster($poster, $adminId);
                    $heroMain->update(['poster_media_id' => $asset->id]);
                } finally {
                    $poster->delete();
                }
            } elseif (is_string($poster) && $poster !== '') {
                $fullPath = Storage::disk('local')->path($poster);
                if (is_file($fullPath)) {
                    try {
                        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                        $asset = $service->storePoster($file, $adminId);
                        $heroMain->update(['poster_media_id' => $asset->id]);
                    } finally {
                        @unlink($fullPath);
                    }
                }
            }
        }

        $heroEventsData = $data['hero_events'] ?? null;
        if (is_array($heroEventsData)) {
            $heroEvents = HeroVideo::firstOrCreate(
                ['page' => HeroVideo::PAGE_EVENTS],
                ['is_enabled' => true]
            );
            $heroEvents->update([
                'is_enabled' => (bool) ($heroEventsData['is_enabled'] ?? true),
                'title' => $heroEventsData['title'] ?? null,
                'button_text' => $heroEventsData['button_text'] ?? null,
                'button_url' => $heroEventsData['button_url'] ?? null,
            ]);
            $service = app(HeroVideoUploadService::class);
            $adminId = auth()->id();

            $video = $heroEventsData['video_upload'] ?? null;
            if (is_array($video)) {
                $video = array_values($video)[0] ?? null;
            }
            if ($video instanceof TemporaryUploadedFile && $video->exists()) {
                try {
                    $asset = $service->storeVideo($video, $adminId);
                    $heroEvents->update(['video_media_id' => $asset->id]);
                } finally {
                    $video->delete();
                }
            } elseif (is_string($video) && $video !== '') {
                $fullPath = Storage::disk('local')->path($video);
                if (is_file($fullPath)) {
                    try {
                        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                        $asset = $service->storeVideo($file, $adminId);
                        $heroEvents->update(['video_media_id' => $asset->id]);
                    } finally {
                        @unlink($fullPath);
                    }
                }
            }

            $poster = $heroEventsData['poster_upload'] ?? null;
            if (is_array($poster)) {
                $poster = array_values($poster)[0] ?? null;
            }
            if ($poster instanceof TemporaryUploadedFile && $poster->exists()) {
                try {
                    $asset = $service->storePoster($poster, $adminId);
                    $heroEvents->update(['poster_media_id' => $asset->id]);
                } finally {
                    $poster->delete();
                }
            } elseif (is_string($poster) && $poster !== '') {
                $fullPath = Storage::disk('local')->path($poster);
                if (is_file($fullPath)) {
                    try {
                        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                        $asset = $service->storePoster($file, $adminId);
                        $heroEvents->update(['poster_media_id' => $asset->id]);
                    } finally {
                        @unlink($fullPath);
                    }
                }
            }
        }

        $heroShopData = $data['hero_shop'] ?? null;
        if (is_array($heroShopData)) {
            SiteSetting::set(SiteSetting::KEY_SHOP_HERO_OVERLAY_OPACITY, (string) ($heroShopData['overlay_opacity'] ?? '0.5'));
            $imageService = app(ImageOptimizationService::class);
            $adminId = auth()->id();
            foreach ([1 => 'slide_1_upload', 2 => 'slide_2_upload', 3 => 'slide_3_upload'] as $idx => $key) {
                $upload = $heroShopData[$key] ?? null;
                if (is_array($upload)) {
                    $upload = array_values($upload)[0] ?? null;
                }
                $mediaId = null;
                if ($upload instanceof TemporaryUploadedFile && $upload->exists()) {
                    try {
                        $fullPath = $upload->getRealPath();
                        $asset = $imageService->processUploadFromPath($fullPath, $upload->getClientOriginalName(), $adminId);
                        $mediaId = (string) $asset->id;
                    } finally {
                        $upload->delete();
                    }
                } elseif (is_string($upload) && $upload !== '') {
                    $fullPath = Storage::disk('local')->path($upload);
                    if (is_file($fullPath)) {
                        try {
                            $asset = $imageService->processUploadFromPath($fullPath, basename($fullPath), $adminId);
                            $mediaId = (string) $asset->id;
                        } finally {
                            @unlink($fullPath);
                        }
                    }
                }
                $settingKey = match ($idx) {
                    1 => SiteSetting::KEY_SHOP_HERO_SLIDE_1_MEDIA_ID,
                    2 => SiteSetting::KEY_SHOP_HERO_SLIDE_2_MEDIA_ID,
                    3 => SiteSetting::KEY_SHOP_HERO_SLIDE_3_MEDIA_ID,
                    default => null,
                };
                if ($settingKey) {
                    if ($mediaId !== null) {
                        SiteSetting::set($settingKey, $mediaId);
                    }
                }
            }
        }

        foreach (SitePage::slugs() as $slug) {
            $row = $data['pages'][$slug] ?? null;
            if ($row === null) {
                continue;
            }
            SitePage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $row['title'] ?? '',
                    'content' => $row['content'] ?? '',
                ]
            );
        }

        if (isset($data['home_stats']) && is_array($data['home_stats'])) {
            foreach ($data['home_stats'] as $i => $item) {
                $idx = (int) $i + 1;
                if ($idx >= 1 && $idx <= 4 && is_array($item)) {
                    SiteSetting::set("home_stat_{$idx}_number", $item['number'] ?? '');
                    SiteSetting::set("home_stat_{$idx}_label", $item['label'] ?? '');
                    SiteSetting::set("home_stat_{$idx}_desc", $item['desc'] ?? '');
                }
            }
        }

        if (isset($data['contact']) && is_array($data['contact'])) {
            $map = [
                'vk_url' => SiteSetting::KEY_VK_URL,
                'telegram_url' => SiteSetting::KEY_TELEGRAM_URL,
                'rutube_url' => SiteSetting::KEY_RUTUBE_URL,
                'email' => SiteSetting::KEY_EMAIL,
                'phone' => SiteSetting::KEY_PHONE,
                'schedule_weekdays' => SiteSetting::KEY_SCHEDULE_WEEKDAYS,
                'schedule_events' => SiteSetting::KEY_SCHEDULE_EVENTS,
                'schedule_note' => SiteSetting::KEY_SCHEDULE_NOTE,
                'company_name' => SiteSetting::KEY_COMPANY_NAME,
                'inn' => SiteSetting::KEY_INN,
                'kpp' => SiteSetting::KEY_KPP,
                'ogrn' => SiteSetting::KEY_OGRN,
            ];
            foreach ($map as $formKey => $settingKey) {
                SiteSetting::set($settingKey, $data['contact'][$formKey] ?? null);
            }
        }

        Notification::make()
            ->title('Контент сайта сохранён')
            ->success()
            ->send();
    }

    protected function defaultTitle(string $slug): string
    {
        return match ($slug) {
            SitePage::SLUG_ABOUT => 'О команде организатора',
            SitePage::SLUG_RULES => 'Правила забега',
            SitePage::SLUG_PRIVACY => 'Политика конфиденциальности',
            SitePage::SLUG_TERMS => 'Условия продажи мерча',
            SitePage::SLUG_CONSENT => 'Согласие на обработку данных',
            SitePage::SLUG_RETURNS => 'Возврат и обмен',
            default => '',
        };
    }

    protected function defaultHomeStatNumber(int $i): string
    {
        return match ($i) {
            1 => '12',
            2 => '34',
            3 => '300+',
            4 => '600+',
            default => '',
        };
    }

    protected function defaultHomeStatLabel(int $i): string
    {
        return match ($i) {
            1 => 'оригинальных тематических забегов',
            2 => 'увлекательных маршрутов',
            3 => 'ключевых локаций',
            4 => 'интеллектуальных и активных заданий',
            default => '',
        };
    }

    protected function defaultHomeStatDesc(int $i): string
    {
        return match ($i) {
            1 => 'Получайте очки для победы в общем зачёте',
            2 => 'Определяйте лучшую логистику для победы',
            3 => 'Узнавайте редкие места, погружайтесь в легендарные истории',
            4 => 'Разгадывайте и узнавайте',
            default => '',
        };
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить')
                ->action(function (): void {
                    $this->save();
                })
                ->keyBindings(['mod+s']),
        ];
    }
}
