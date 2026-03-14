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
use League\Flysystem\UnableToRetrieveMetadata;
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

        $aboutTeamJson = SiteSetting::get(SiteSetting::KEY_ABOUT_TEAM_MEMBERS);
        $data['about'] = [
            'hero_title' => SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_TITLE, 'Команда'),
            'hero_subtitle' => SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_SUBTITLE, 'Люди, которые делают Urban Running Games — забеги-игры в вашем городе'),
            'mission_title' => SiteSetting::get(SiteSetting::KEY_ABOUT_MISSION_TITLE, 'Наша миссия'),
            'mission_content' => SiteSetting::get(SiteSetting::KEY_ABOUT_MISSION_CONTENT, ''),
            'team_members' => $aboutTeamJson ? (json_decode($aboutTeamJson, true) ?: []) : [],
        ];
        if (empty($data['about']['mission_content'])) {
            $data['about']['mission_content'] = "<p>Делаем городские забеги-игры доступными и по-настоящему весёлыми. Объединяем людей через движение, азарт и командный дух.</p><p>Если хотите стать партнёром или присоединиться к организации событий — пишите нам в <a href=\"/contact\">Контакты</a>.</p>";
        }

        $data['contact'] = [
            'vk_url' => SiteSetting::get(SiteSetting::KEY_VK_URL, 'https://vk.com/urbanrunninggames'),
            'telegram_url' => SiteSetting::get(SiteSetting::KEY_TELEGRAM_URL, 'https://t.me/urbanrunninggames'),
            'rutube_url' => SiteSetting::get(SiteSetting::KEY_RUTUBE_URL, '#'),
            'email' => SiteSetting::get(SiteSetting::KEY_EMAIL, 'main@sprut.run'),
            'phone' => SiteSetting::get(SiteSetting::KEY_PHONE, '+79178060995'),
            'schedule_weekdays' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_WEEKDAYS, 'Понедельник–пятница <br> 9:00–18:00'),
            'schedule_events' => SiteSetting::get(SiteSetting::KEY_SCHEDULE_EVENTS, 'В дни мероприятий <br> 6:00–0:00'),
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

        $infoAccordionJson = SiteSetting::get(SiteSetting::KEY_HOME_INFO_ACCORDION_ITEMS);
        $data['info_section_title'] = SiteSetting::get(SiteSetting::KEY_HOME_INFO_SECTION_TITLE, 'ИНФОРМАЦИЯ');
        $data['info_accordion_items'] = $infoAccordionJson ? (json_decode($infoAccordionJson, true) ?: $this->defaultInfoAccordionItems()) : $this->defaultInfoAccordionItems();

        $data['hero_main'] = [
            'is_enabled' => HeroVideo::where('page', HeroVideo::PAGE_MAIN)->value('is_enabled') ?? true,
            'video_upload' => null,
            'video_mobile_upload' => null,
            'poster_upload' => null,
            'poster_mobile_upload' => null,
            'title' => HeroVideo::where('page', HeroVideo::PAGE_MAIN)->value('title'),
            'button_text' => HeroVideo::where('page', HeroVideo::PAGE_MAIN)->value('button_text'),
            'button_url' => HeroVideo::where('page', HeroVideo::PAGE_MAIN)->value('button_url'),
            'ornament_upload' => null,
            'ornament_desktop_upload' => null,
            'ornament_remove' => SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_DISABLED) === '1',
            'ornament_opacity' => SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_OPACITY, '0.85'),
        ];

        $data['hero_about'] = [
            'overlay_opacity' => SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_OVERLAY_OPACITY, '0.35'),
            'background_upload' => null,
        ];

        $data['page_text'] = [
            'events_title' => SiteSetting::get(SiteSetting::KEY_EVENTS_PAGE_TITLE, 'Urban Running games'),
            'events_subtitle' => SiteSetting::get(SiteSetting::KEY_EVENTS_PAGE_SUBTITLE, 'Забеги-игры в формате городского ориентирования: бегите по маршруту, выполняйте задания на чекпоинтах и соревнуйтесь в командном зачёте. Каждая гонка — это уникальная тематика, нестандартные точки на карте и настоящий драйв для тех, кто любит движение и вызов.'),
            'shop_title' => SiteSetting::get(SiteSetting::KEY_SHOP_PAGE_TITLE, 'SPRUT STYLE STORE'),
            'shop_subtitle' => SiteSetting::get(SiteSetting::KEY_SHOP_PAGE_SUBTITLE, 'Мерч для тех, кто бегает с характером. Футболки, худи и аксессуары с фирменным дизайном — бери на гонку или носи каждый день.'),
            'contact_title' => SiteSetting::get(SiteSetting::KEY_CONTACT_PAGE_TITLE, 'Контакты'),
            'contact_subtitle' => SiteSetting::get(SiteSetting::KEY_CONTACT_PAGE_SUBTITLE, 'Вопросы, партнёрство, забеги — напишите нам, мы ответим.'),
            'partners_title' => SiteSetting::get(SiteSetting::KEY_PARTNERS_PAGE_TITLE, 'Партнёры и спонсоры'),
            'partners_subtitle' => SiteSetting::get(SiteSetting::KEY_PARTNERS_PAGE_SUBTITLE, 'Компании и люди, которые делают Urban Running Games возможными'),
            'partners_cta_title' => SiteSetting::get(SiteSetting::KEY_PARTNERS_CTA_TITLE, 'Хотите стать партнёром?'),
            'rating_title' => SiteSetting::get(SiteSetting::KEY_RATING_PAGE_TITLE, 'Сводный рейтинг'),
            'rating_subtitle' => SiteSetting::get(SiteSetting::KEY_RATING_PAGE_SUBTITLE, 'Рейтинг команд по результатам всех проведённых забегов-игр'),
            'gallery_title' => SiteSetting::get(SiteSetting::KEY_GALLERY_PAGE_TITLE, 'Фото'),
        ];

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Главная')
                            ->icon('heroicon-o-home')
                            ->schema($this->homePageSchema()),
                        Forms\Components\Tabs\Tab::make('Гонки')
                            ->icon('heroicon-o-fire')
                            ->schema($this->eventsPageSchema()),
                        Forms\Components\Tabs\Tab::make('Магазин')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema($this->shopPageSchema()),
                        Forms\Components\Tabs\Tab::make('О команде')
                            ->icon('heroicon-o-user-group')
                            ->schema($this->aboutPageSchema()),
                        Forms\Components\Tabs\Tab::make('Партнёры')
                            ->icon('heroicon-o-hand-thumb-up')
                            ->schema($this->partnersPageSchema()),
                        Forms\Components\Tabs\Tab::make('Контакты')
                            ->icon('heroicon-o-phone')
                            ->schema($this->contactSchema()),
                        Forms\Components\Tabs\Tab::make('Рейтинг')
                            ->icon('heroicon-o-trophy')
                            ->schema($this->ratingPageSchema()),
                        Forms\Components\Tabs\Tab::make('Галерея')
                            ->icon('heroicon-o-photo')
                            ->schema($this->galleryPageSchema()),
                        Forms\Components\Tabs\Tab::make('Правила')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema($this->rulesPageSchema()),
                        Forms\Components\Tabs\Tab::make('Юридические')
                            ->icon('heroicon-o-scale')
                            ->schema($this->legalPagesSchema()),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    // ─── Главная ───────────────────────────────────────────────

    protected function homePageSchema(): array
    {
        $heroMain = HeroVideo::with(['videoMedia', 'videoMobileMedia', 'posterMedia', 'posterMobileMedia'])
            ->where('page', HeroVideo::PAGE_MAIN)->first();

        return [
            Forms\Components\Section::make('Hero-видео главной страницы')
                ->description('Видео и постер для hero-блока. Для мобильных устройств загрузите отдельные версии, чтобы избежать обрезки.')
                ->schema([
                    Forms\Components\Toggle::make('hero_main.is_enabled')
                        ->label('Включено')
                        ->default(true),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\FileUpload::make('hero_main.video_upload')
                            ->label('Видео для десктопа (mp4)')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(100 * 1024 * 1024)
                            ->disk('local')->directory('livewire-tmp')->visibility('private')
                            ->nullable()->storeFiles(false),
                        Forms\Components\FileUpload::make('hero_main.video_mobile_upload')
                            ->label('Видео для мобильных (mp4)')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(100 * 1024 * 1024)
                            ->disk('local')->directory('livewire-tmp')->visibility('private')
                            ->nullable()->storeFiles(false)
                            ->helperText('Вертикальное или квадратное видео для смартфонов.'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('hero_main.current_video')
                            ->label('Текущее видео (десктоп)')
                            ->content($heroMain && $heroMain->videoMedia
                                ? new \Illuminate\Support\HtmlString('<a href="' . e($heroMain->video_url) . '" target="_blank">' . e($heroMain->videoMedia->original_name ?? 'Видео') . '</a>')
                                : '—'),
                        Forms\Components\Placeholder::make('hero_main.current_video_mobile')
                            ->label('Текущее видео (мобильное)')
                            ->content($heroMain && $heroMain->videoMobileMedia
                                ? new \Illuminate\Support\HtmlString('<a href="' . e($heroMain->video_mobile_url) . '" target="_blank">' . e($heroMain->videoMobileMedia->original_name ?? 'Видео') . '</a>')
                                : '—'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\FileUpload::make('hero_main.poster_upload')
                            ->label('Постер для десктопа')
                            ->image()->imagePreviewHeight(120)
                            ->disk('local')->directory('livewire-tmp')->visibility('private')
                            ->nullable()->storeFiles(false),
                        Forms\Components\FileUpload::make('hero_main.poster_mobile_upload')
                            ->label('Постер для мобильных')
                            ->image()->imagePreviewHeight(120)
                            ->disk('local')->directory('livewire-tmp')->visibility('private')
                            ->nullable()->storeFiles(false)
                            ->helperText('Вертикальный постер для смартфонов.'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('hero_main.current_poster')
                            ->label('Текущий постер (десктоп)')
                            ->content($heroMain && $heroMain->posterMedia?->url
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($heroMain->poster_url) . '" alt="" style="max-height:120px;">')
                                : '—'),
                        Forms\Components\Placeholder::make('hero_main.current_poster_mobile')
                            ->label('Текущий постер (мобильный)')
                            ->content($heroMain && $heroMain->posterMobileMedia?->url
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($heroMain->poster_mobile_url) . '" alt="" style="max-height:120px;">')
                                : '—'),
                    ]),
                    Forms\Components\TextInput::make('hero_main.title')
                        ->label('Заголовок')->maxLength(255)->nullable()
                        ->placeholder('Urban Running Games'),
                    Forms\Components\TextInput::make('hero_main.button_text')
                        ->label('Текст кнопки')->maxLength(64)->nullable()
                        ->placeholder('Предстоящие события'),
                    Forms\Components\TextInput::make('hero_main.button_url')
                        ->label('URL кнопки')->maxLength(512)->nullable()
                        ->placeholder('/events'),
                    Forms\Components\Placeholder::make('ornament_heading')
                        ->label('Орнамент поверх hero-видео')
                        ->content('Орнамент накладывается поверх видео. Можно загрузить разные версии для мобильных и десктопа.')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('hero_main.ornament_remove')
                        ->label('Убрать орнамент')
                        ->default(false),
                    Forms\Components\TextInput::make('hero_main.ornament_opacity')
                        ->label('Прозрачность орнамента (0–1)')
                        ->numeric()->minValue(0)->maxValue(1)->step(0.05)->default(0.85)
                        ->visible(fn (\Filament\Forms\Get $get): bool => !($get('ornament_remove') ?? false)),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\FileUpload::make('hero_main.ornament_upload')
                            ->label('Орнамент для мобильных')
                            ->acceptedFileTypes(['image/svg+xml', 'image/svg', 'image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(2 * 1024 * 1024)
                            ->disk('local')->directory('livewire-tmp')->visibility('private')
                            ->nullable()->storeFiles(false),
                        Forms\Components\FileUpload::make('hero_main.ornament_desktop_upload')
                            ->label('Орнамент для десктопа')
                            ->acceptedFileTypes(['image/svg+xml', 'image/svg', 'image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(2 * 1024 * 1024)
                            ->disk('local')->directory('livewire-tmp')->visibility('private')
                            ->nullable()->storeFiles(false),
                    ])->visible(fn (\Filament\Forms\Get $get): bool => !($get('ornament_remove') ?? false)),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('hero_main.current_ornament')
                            ->label('Текущий орнамент (мобильный)')
                            ->content(function () {
                                $id = SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_MEDIA_ID);
                                if ($id && $id !== 'none') {
                                    $asset = MediaAsset::find($id);
                                    return $asset ? new \Illuminate\Support\HtmlString('<img src="' . e($asset->thumbnail_url ?? $asset->url) . '" alt="" style="max-height:80px;">') : 'Загруженный файл';
                                }
                                return 'По умолчанию: maze-1.svg';
                            }),
                        Forms\Components\Placeholder::make('hero_main.current_ornament_desktop')
                            ->label('Текущий орнамент (десктоп)')
                            ->content(function () {
                                $id = SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_DESKTOP_MEDIA_ID);
                                if ($id && $id !== 'none') {
                                    $asset = MediaAsset::find($id);
                                    return $asset ? new \Illuminate\Support\HtmlString('<img src="' . e($asset->thumbnail_url ?? $asset->url) . '" alt="" style="max-height:80px;">') : 'Загруженный файл';
                                }
                                return 'Используется мобильный орнамент';
                            }),
                    ])->visible(fn (\Filament\Forms\Get $get): bool => !($get('ornament_remove') ?? false)),
                ])
                ->columns(1)->collapsible()->collapsed(),

            Forms\Components\Section::make('Блок статистики (цифры на главной)')
                ->schema([
                    Forms\Components\Repeater::make('home_stats')
                        ->label('')
                        ->schema([
                            Forms\Components\TextInput::make('number')->label('Число')->maxLength(32)->required(),
                            Forms\Components\TextInput::make('label')->label('Подпись')->maxLength(255)->required(),
                            Forms\Components\TextInput::make('desc')->label('Описание')->maxLength(255)->nullable(),
                        ])
                        ->defaultItems(4)->minItems(4)->maxItems(4)->columnSpanFull(),
                ])->collapsible()->collapsed(),

            Forms\Components\Section::make('Блок «Информация» на главной (аккордеон)')
                ->schema([
                    Forms\Components\TextInput::make('info_section_title')
                        ->label('Заголовок секции')->maxLength(255)->default('ИНФОРМАЦИЯ')->required(),
                    Forms\Components\Repeater::make('info_accordion_items')
                        ->label('Пункты аккордеона')
                        ->schema([
                            Forms\Components\TextInput::make('title')->label('Заголовок пункта')->required()->maxLength(512),
                            Forms\Components\Select::make('content_type')
                                ->label('Тип контента')
                                ->options(['links' => 'Список ссылок', 'prose' => 'Текст (HTML)'])
                                ->default('links')->required()->live(),
                            Forms\Components\Repeater::make('links')
                                ->label('Ссылки')
                                ->schema([
                                    Forms\Components\TextInput::make('text')->label('Текст ссылки')->required()->maxLength(255),
                                    Forms\Components\TextInput::make('url')->label('URL')->required()->maxLength(1024)->placeholder('/events или https://...'),
                                ])
                                ->defaultItems(0)->addActionLabel('Добавить ссылку')->columnSpanFull()
                                ->visible(fn (\Filament\Forms\Get $get): bool => ($get('content_type') ?? '') === 'links'),
                            Forms\Components\RichEditor::make('content')
                                ->label('Текст (HTML)')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])
                                ->columnSpanFull()
                                ->visible(fn (\Filament\Forms\Get $get): bool => ($get('content_type') ?? '') === 'prose'),
                        ])
                        ->defaultItems(0)->addActionLabel('Добавить блок')->reorderable()->columnSpanFull(),
                ])->collapsible()->collapsed(),
        ];
    }

    // ─── Гонки ─────────────────────────────────────────────────

    protected function eventsPageSchema(): array
    {
        return [
            Forms\Components\Section::make('Текст на странице «Гонки»')
                ->description('Заголовок и вводный текст на странице /events. Содержимое гонок управляется через раздел «События».')
                ->schema([
                    Forms\Components\TextInput::make('page_text.events_title')
                        ->label('Заголовок (H1)')
                        ->maxLength(255)
                        ->placeholder('Urban Running games'),
                    Forms\Components\Textarea::make('page_text.events_subtitle')
                        ->label('Подзаголовок')
                        ->rows(3)
                        ->maxLength(2000),
                ])->columns(1),
        ];
    }

    // ─── Магазин ───────────────────────────────────────────────

    protected function shopPageSchema(): array
    {
        return [
            Forms\Components\Section::make('Текст на странице «Магазин»')
                ->description('Заголовок и вводный текст на странице /shop. Товары управляются через раздел «Товары».')
                ->schema([
                    Forms\Components\TextInput::make('page_text.shop_title')
                        ->label('Заголовок (H1)')
                        ->maxLength(255)
                        ->placeholder('SPRUT STYLE STORE'),
                    Forms\Components\Textarea::make('page_text.shop_subtitle')
                        ->label('Подзаголовок')
                        ->rows(3)
                        ->maxLength(2000),
                ])->columns(1),
        ];
    }

    // ─── О команде ─────────────────────────────────────────────

    protected function aboutPageSchema(): array
    {
        $aboutHeroBg = $this->getAboutHeroBackgroundMedia();

        return [
            Forms\Components\Section::make('Hero-блок страницы «О команде»')
                ->schema([
                    Forms\Components\TextInput::make('about.hero_title')
                        ->label('Заголовок в Hero')->maxLength(255)->default('Команда'),
                    Forms\Components\TextInput::make('about.hero_subtitle')
                        ->label('Подзаголовок в Hero')->maxLength(512)->columnSpanFull(),
                    Forms\Components\TextInput::make('hero_about.overlay_opacity')
                        ->label('Непрозрачность оверлея (0–1)')
                        ->numeric()->minValue(0)->maxValue(1)->step(0.05)->default(0.35),
                    Forms\Components\FileUpload::make('hero_about.background_upload')
                        ->label('Фоновое изображение')
                        ->image()->maxSize(50 * 1024)
                        ->disk('local')->directory('livewire-tmp')->visibility('private')
                        ->nullable()->storeFiles(false),
                    Forms\Components\Placeholder::make('hero_about.current_background')
                        ->label('Текущий фон')
                        ->content($aboutHeroBg
                            ? new \Illuminate\Support\HtmlString('<img src="' . e($aboutHeroBg->thumbnail_url ?? $aboutHeroBg->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                            : '—'),
                ])->columns(1)->collapsible()->collapsed(),

            Forms\Components\Section::make('Контент страницы')
                ->schema([
                    Forms\Components\TextInput::make('pages.about.title')
                        ->label('Заголовок страницы (для тега title)')->maxLength(255),
                    Forms\Components\RichEditor::make('pages.about.content')
                        ->label('Вводный текст (под Hero)')
                        ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('about.mission_title')
                        ->label('Заголовок блока «Миссия»')->maxLength(255)->default('Наша миссия'),
                    Forms\Components\RichEditor::make('about.mission_content')
                        ->label('Текст блока «Миссия»')
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'redo', 'undo'])
                        ->columnSpanFull(),
                ])->collapsible()->collapsed(),

            Forms\Components\Section::make('Участники команды')
                ->description('Загрузите фотографии участников команды.')
                ->schema([
                    Forms\Components\Repeater::make('about.team_members')
                        ->label('')
                        ->schema([
                            Forms\Components\FileUpload::make('photo_upload')
                                ->label('Фото')
                                ->image()->maxSize(50 * 1024)
                                ->disk('local')->directory('livewire-tmp')->visibility('private')
                                ->nullable()->storeFiles(false),
                            Forms\Components\Placeholder::make('photo_preview')
                                ->label('Текущее фото')
                                ->content(function (\Filament\Forms\Get $get): \Illuminate\Support\HtmlString|string {
                                    $id = $get('photo_media_id');
                                    if (!$id) return '—';
                                    $asset = MediaAsset::find($id);
                                    return $asset
                                        ? new \Illuminate\Support\HtmlString('<img src="' . e($asset->thumbnail_url ?? $asset->url) . '" alt="" style="max-width:120px;height:auto;border-radius:8px;">')
                                        : '—';
                                })
                                ->visible(fn (\Filament\Forms\Get $get): bool => !empty($get('photo_media_id'))),
                        ])
                        ->defaultItems(0)->reorderable()->columnSpanFull(),
                ])->collapsible()->collapsed(),
        ];
    }

    // ─── Контакты ──────────────────────────────────────────────

    protected function contactSchema(): array
    {
        return [
            Forms\Components\Section::make('Текст на странице «Контакты»')
                ->schema([
                    Forms\Components\TextInput::make('page_text.contact_title')
                        ->label('Заголовок (H1)')
                        ->maxLength(255)
                        ->placeholder('Контакты'),
                    Forms\Components\Textarea::make('page_text.contact_subtitle')
                        ->label('Подзаголовок')
                        ->rows(2)
                        ->maxLength(1000),
                ])->columns(1)->collapsible()->collapsed(),
            Forms\Components\Section::make('Ссылки на соцсети и мессенджеры')
                ->schema([
                    Forms\Components\TextInput::make('contact.vk_url')->label('VK (URL)')->url()->maxLength(512),
                    Forms\Components\TextInput::make('contact.telegram_url')->label('Telegram (URL)')->url()->maxLength(512),
                    Forms\Components\TextInput::make('contact.rutube_url')->label('RuTube (URL)')->url()->maxLength(512),
                ])->columns(1)->collapsible()->collapsed(),
            Forms\Components\Section::make('Контактные данные')
                ->schema([
                    Forms\Components\TextInput::make('contact.email')->label('Email')->email()->maxLength(255),
                    Forms\Components\TextInput::make('contact.phone')->label('Телефон')->tel()->maxLength(64),
                ])->columns(1)->collapsible()->collapsed(),
            Forms\Components\Section::make('Режим работы (текст в футере)')
                ->schema([
                    Forms\Components\TextInput::make('contact.schedule_weekdays')->label('Будни')->maxLength(255),
                    Forms\Components\TextInput::make('contact.schedule_events')->label('В дни мероприятий')->maxLength(255),
                    Forms\Components\TextInput::make('contact.schedule_note')->label('Доп. строка')->maxLength(255),
                ])->columns(1)->collapsible()->collapsed(),
            Forms\Components\Section::make('Реквизиты компании (футер)')
                ->schema([
                    Forms\Components\TextInput::make('contact.company_name')->label('Название')->maxLength(255),
                    Forms\Components\TextInput::make('contact.inn')->label('ИНН')->maxLength(32),
                    Forms\Components\TextInput::make('contact.kpp')->label('КПП')->maxLength(32),
                    Forms\Components\TextInput::make('contact.ogrn')->label('ОГРН')->maxLength(32),
                ])->columns(1)->collapsible()->collapsed(),
        ];
    }

    // ─── Партнёры ──────────────────────────────────────────────

    protected function partnersPageSchema(): array
    {
        return [
            Forms\Components\Section::make('Текст на странице «Партнёры»')
                ->description('Заголовки и вводный текст на странице /partners. Партнёры и спонсоры управляются через раздел «Партнёры».')
                ->schema([
                    Forms\Components\TextInput::make('page_text.partners_title')
                        ->label('Заголовок (H1)')
                        ->maxLength(255)
                        ->placeholder('Партнёры и спонсоры'),
                    Forms\Components\Textarea::make('page_text.partners_subtitle')
                        ->label('Подзаголовок')
                        ->rows(2)
                        ->maxLength(1000),
                    Forms\Components\TextInput::make('page_text.partners_cta_title')
                        ->label('Заголовок CTA-блока «Хотите стать партнёром?»')
                        ->maxLength(255)
                        ->placeholder('Хотите стать партнёром?'),
                ])->columns(1),
        ];
    }

    // ─── Рейтинг ──────────────────────────────────────────────

    protected function ratingPageSchema(): array
    {
        return [
            Forms\Components\Section::make('Текст на странице «Рейтинг»')
                ->description('Заголовок и подзаголовок на странице /rating. Данные рейтинга управляются через раздел «Рейтинг».')
                ->schema([
                    Forms\Components\TextInput::make('page_text.rating_title')
                        ->label('Заголовок (H1)')
                        ->maxLength(255)
                        ->placeholder('Сводный рейтинг'),
                    Forms\Components\Textarea::make('page_text.rating_subtitle')
                        ->label('Подзаголовок')
                        ->rows(2)
                        ->maxLength(1000),
                ])->columns(1),
        ];
    }

    // ─── Галерея ──────────────────────────────────────────────

    protected function galleryPageSchema(): array
    {
        return [
            Forms\Components\Section::make('Текст на странице «Галерея»')
                ->description('Заголовок на странице /gallery. Альбомы управляются через раздел «Альбомы».')
                ->schema([
                    Forms\Components\TextInput::make('page_text.gallery_title')
                        ->label('Заголовок (H1)')
                        ->maxLength(255)
                        ->placeholder('Фото'),
                ])->columns(1),
        ];
    }

    // ─── Правила ───────────────────────────────────────────────

    protected function rulesPageSchema(): array
    {
        return [
            Forms\Components\Section::make('Правила забега')
                ->schema([
                    Forms\Components\TextInput::make('pages.rules.title')->label('Заголовок')->maxLength(255),
                    Forms\Components\RichEditor::make('pages.rules.content')
                        ->label('Текст (HTML)')
                        ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])
                        ->columnSpanFull(),
                ])->collapsible()->collapsed(),
        ];
    }

    // ─── Юридические ───────────────────────────────────────────

    protected function legalPagesSchema(): array
    {
        $make = fn (string $label, string $slug) => Forms\Components\Section::make($label)
            ->schema([
                Forms\Components\TextInput::make("pages.{$slug}.title")->label('Заголовок')->maxLength(255),
                Forms\Components\RichEditor::make("pages.{$slug}.content")
                    ->label('Текст')
                    ->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'redo', 'undo'])
                    ->columnSpanFull(),
            ])->collapsible()->collapsed();

        return [
            $make('Политика конфиденциальности', 'privacy'),
            $make('Условия продажи мерча', 'terms'),
            $make('Согласие на обработку данных', 'consent'),
            $make('Возврат и обмен', 'returns'),
            $make('Где жить, как добраться до места старта', 'travel'),
        ];
    }

    // ─── Helpers ────────────────────────────────────────────────

    protected function getAboutHeroBackgroundMedia(): ?MediaAsset
    {
        $id = SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_BACKGROUND_MEDIA_ID);
        return $id ? MediaAsset::find($id) : null;
    }

    // ─── Save ──────────────────────────────────────────────────

    public function save(): void
    {
        try {
            $data = $this->form->getState();
        } catch (UnableToRetrieveMetadata $e) {
            $data = $this->data;
        }

        $this->saveHeroMain($data['hero_main'] ?? null);
        $this->saveHeroAbout($data['hero_about'] ?? null);
        $this->savePages($data['pages'] ?? []);
        $this->saveAbout($data['about'] ?? null);
        $this->saveHomeStats($data['home_stats'] ?? null);
        $this->saveInfoAccordion($data);
        $this->saveContact($data['contact'] ?? null);
        $this->savePageTexts($data['page_text'] ?? null);

        Notification::make()
            ->title('Контент сайта сохранён')
            ->success()
            ->send();
    }

    protected function saveHeroMain(?array $heroMainData): void
    {
        if (!is_array($heroMainData)) return;

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

        $this->processVideoUpload($heroMainData['video_upload'] ?? null, $heroMain, 'video_media_id', $service, $adminId);
        $this->processVideoUpload($heroMainData['video_mobile_upload'] ?? null, $heroMain, 'video_mobile_media_id', $service, $adminId);
        $this->processImageUpload($heroMainData['poster_upload'] ?? null, $heroMain, 'poster_media_id', $service, $adminId, true);
        $this->processImageUpload($heroMainData['poster_mobile_upload'] ?? null, $heroMain, 'poster_mobile_media_id', $service, $adminId, true);

        $ornamentRemove = (bool) ($heroMainData['ornament_remove'] ?? false);
        SiteSetting::set(SiteSetting::KEY_HERO_ORNAMENT_DISABLED, $ornamentRemove ? '1' : '0');

        if (!$ornamentRemove) {
            SiteSetting::set(SiteSetting::KEY_HERO_ORNAMENT_OPACITY, (string) ($heroMainData['ornament_opacity'] ?? '0.85'));
            $this->processOrnamentUpload($heroMainData['ornament_upload'] ?? null, SiteSetting::KEY_HERO_ORNAMENT_MEDIA_ID);
            $this->processOrnamentUpload($heroMainData['ornament_desktop_upload'] ?? null, SiteSetting::KEY_HERO_ORNAMENT_DESKTOP_MEDIA_ID);
        } else {
            SiteSetting::set(SiteSetting::KEY_HERO_ORNAMENT_MEDIA_ID, '');
            SiteSetting::set(SiteSetting::KEY_HERO_ORNAMENT_DESKTOP_MEDIA_ID, '');
        }
    }

    protected function processVideoUpload($upload, HeroVideo $heroVideo, string $column, HeroVideoUploadService $service, ?int $adminId): void
    {
        if (is_array($upload)) $upload = array_values($upload)[0] ?? null;
        if ($upload instanceof TemporaryUploadedFile && $upload->exists()) {
            try {
                $asset = $service->storeVideo($upload, $adminId);
                $heroVideo->update([$column => $asset->id]);
            } finally {
                $upload->delete();
            }
        } elseif (is_string($upload) && $upload !== '') {
            $fullPath = Storage::disk('local')->path($upload);
            if (is_file($fullPath)) {
                try {
                    $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                    $asset = $service->storeVideo($file, $adminId);
                    $heroVideo->update([$column => $asset->id]);
                } finally {
                    @unlink($fullPath);
                }
            }
        }
    }

    protected function processImageUpload($upload, HeroVideo $heroVideo, string $column, HeroVideoUploadService $service, ?int $adminId, bool $isPoster = false): void
    {
        if (is_array($upload)) $upload = array_values($upload)[0] ?? null;
        if ($upload instanceof TemporaryUploadedFile && $upload->exists()) {
            try {
                $asset = $service->storePoster($upload, $adminId);
                $heroVideo->update([$column => $asset->id]);
            } finally {
                $upload->delete();
            }
        } elseif (is_string($upload) && $upload !== '') {
            $fullPath = Storage::disk('local')->path($upload);
            if (is_file($fullPath)) {
                try {
                    $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                    $asset = $service->storePoster($file, $adminId);
                    $heroVideo->update([$column => $asset->id]);
                } finally {
                    @unlink($fullPath);
                }
            }
        }
    }

    protected function processOrnamentUpload($upload, string $settingKey): void
    {
        if (is_array($upload)) $upload = array_values($upload)[0] ?? null;
        if ($upload instanceof TemporaryUploadedFile && $upload->exists()) {
            $imageService = app(ImageOptimizationService::class);
            try {
                $asset = $imageService->processUploadFromPath($upload->getRealPath(), $upload->getClientOriginalName(), auth()->id());
                SiteSetting::set($settingKey, (string) $asset->id);
            } finally {
                $upload->delete();
            }
        } elseif (is_string($upload) && $upload !== '') {
            $fullPath = Storage::disk('local')->path($upload);
            if (is_file($fullPath)) {
                $imageService = app(ImageOptimizationService::class);
                try {
                    $asset = $imageService->processUploadFromPath($fullPath, basename($fullPath), auth()->id());
                    SiteSetting::set($settingKey, (string) $asset->id);
                } finally {
                    @unlink($fullPath);
                }
            }
        }
    }

    protected function saveHeroAbout(?array $heroAboutData): void
    {
        if (!is_array($heroAboutData)) return;

        SiteSetting::set(SiteSetting::KEY_ABOUT_HERO_OVERLAY_OPACITY, (string) ($heroAboutData['overlay_opacity'] ?? '0.35'));

        $upload = $heroAboutData['background_upload'] ?? null;
        if (is_array($upload)) $upload = array_values($upload)[0] ?? null;

        $mediaId = null;
        if ($upload instanceof TemporaryUploadedFile && $upload->exists()) {
            $imageService = app(ImageOptimizationService::class);
            try {
                $asset = $imageService->processUploadFromPath($upload->getRealPath(), $upload->getClientOriginalName(), auth()->id());
                $mediaId = (string) $asset->id;
            } finally {
                $upload->delete();
            }
        } elseif (is_string($upload) && $upload !== '') {
            $fullPath = Storage::disk('local')->path($upload);
            if (is_file($fullPath)) {
                $imageService = app(ImageOptimizationService::class);
                try {
                    $asset = $imageService->processUploadFromPath($fullPath, basename($fullPath), auth()->id());
                    $mediaId = (string) $asset->id;
                } finally {
                    @unlink($fullPath);
                }
            }
        }
        if ($mediaId !== null) {
            SiteSetting::set(SiteSetting::KEY_ABOUT_HERO_BACKGROUND_MEDIA_ID, $mediaId);
        }
    }

    protected function savePages(array $pagesData): void
    {
        foreach (SitePage::slugs() as $slug) {
            $row = $pagesData[$slug] ?? null;
            if ($row === null) continue;
            SitePage::query()->updateOrCreate(
                ['slug' => $slug],
                ['title' => $row['title'] ?? '', 'content' => $row['content'] ?? '']
            );
        }
    }

    protected function saveAbout(?array $aboutData): void
    {
        if (!is_array($aboutData)) return;

        SiteSetting::set(SiteSetting::KEY_ABOUT_HERO_TITLE, $aboutData['hero_title'] ?? '');
        SiteSetting::set(SiteSetting::KEY_ABOUT_HERO_SUBTITLE, $aboutData['hero_subtitle'] ?? '');
        SiteSetting::set(SiteSetting::KEY_ABOUT_MISSION_TITLE, $aboutData['mission_title'] ?? '');
        SiteSetting::set(SiteSetting::KEY_ABOUT_MISSION_CONTENT, $aboutData['mission_content'] ?? '');

        $team = $aboutData['team_members'] ?? [];
        $teamNormalized = [];
        $imageService = app(ImageOptimizationService::class);
        $adminId = auth()->id();

        foreach ($team as $item) {
            if (!is_array($item)) continue;

            $photoMediaId = $item['photo_media_id'] ?? null;
            $upload = $item['photo_upload'] ?? null;
            if (is_array($upload)) {
                $first = array_values($upload)[0] ?? null;
                if (is_array($first)) {
                    $pathOrFile = $first[0] ?? $first['path'] ?? null;
                    $upload = is_string($pathOrFile)
                        ? (str_starts_with($pathOrFile, 'livewire-file:') ? 'livewire-tmp/' . substr($pathOrFile, strlen('livewire-file:')) : $pathOrFile)
                        : null;
                } else {
                    $upload = $first;
                }
            }

            if ($upload instanceof TemporaryUploadedFile && $upload->exists()) {
                try {
                    $asset = $imageService->processUploadFromPath($upload->getRealPath(), $upload->getClientOriginalName(), $adminId);
                    $photoMediaId = (string) $asset->id;
                } finally {
                    $upload->delete();
                }
            } elseif (is_string($upload) && $upload !== '') {
                $fullPath = Storage::disk('local')->path($upload);
                if (is_file($fullPath)) {
                    try {
                        $asset = $imageService->processUploadFromPath($fullPath, basename($fullPath), $adminId);
                        $photoMediaId = (string) $asset->id;
                    } finally {
                        @unlink($fullPath);
                    }
                }
            }

            $teamNormalized[] = ['photo_media_id' => $photoMediaId];
        }

        SiteSetting::set(SiteSetting::KEY_ABOUT_TEAM_MEMBERS, json_encode($teamNormalized, JSON_UNESCAPED_UNICODE));
    }

    protected function saveHomeStats(?array $homeStats): void
    {
        if (!is_array($homeStats)) return;

        foreach ($homeStats as $i => $item) {
            $idx = (int) $i + 1;
            if ($idx >= 1 && $idx <= 4 && is_array($item)) {
                SiteSetting::set("home_stat_{$idx}_number", $item['number'] ?? '');
                SiteSetting::set("home_stat_{$idx}_label", $item['label'] ?? '');
                SiteSetting::set("home_stat_{$idx}_desc", $item['desc'] ?? '');
            }
        }
    }

    protected function saveInfoAccordion(array $data): void
    {
        if (isset($data['info_section_title'])) {
            SiteSetting::set(SiteSetting::KEY_HOME_INFO_SECTION_TITLE, (string) $data['info_section_title']);
        }

        if (isset($data['info_accordion_items']) && is_array($data['info_accordion_items'])) {
            $normalized = [];
            foreach ($data['info_accordion_items'] as $item) {
                if (!is_array($item) || empty($item['title'])) continue;
                $type = $item['content_type'] ?? 'links';
                $row = ['title' => $item['title'], 'content_type' => $type];
                if ($type === 'links' && !empty($item['links'])) {
                    $row['links'] = [];
                    foreach ($item['links'] as $link) {
                        if (is_array($link) && !empty($link['text']) && isset($link['url'])) {
                            $row['links'][] = ['text' => $link['text'], 'url' => $link['url']];
                        }
                    }
                } elseif ($type === 'prose' && isset($item['content'])) {
                    $row['content'] = $item['content'];
                }
                $normalized[] = $row;
            }
            SiteSetting::set(SiteSetting::KEY_HOME_INFO_ACCORDION_ITEMS, json_encode($normalized, JSON_UNESCAPED_UNICODE));
        }
    }

    protected function saveContact(?array $contactData): void
    {
        if (!is_array($contactData)) return;

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
            SiteSetting::set($settingKey, $contactData[$formKey] ?? null);
        }
    }

    protected function savePageTexts(?array $pageText): void
    {
        if (!is_array($pageText)) return;

        $map = [
            'events_title' => SiteSetting::KEY_EVENTS_PAGE_TITLE,
            'events_subtitle' => SiteSetting::KEY_EVENTS_PAGE_SUBTITLE,
            'shop_title' => SiteSetting::KEY_SHOP_PAGE_TITLE,
            'shop_subtitle' => SiteSetting::KEY_SHOP_PAGE_SUBTITLE,
            'contact_title' => SiteSetting::KEY_CONTACT_PAGE_TITLE,
            'contact_subtitle' => SiteSetting::KEY_CONTACT_PAGE_SUBTITLE,
            'partners_title' => SiteSetting::KEY_PARTNERS_PAGE_TITLE,
            'partners_subtitle' => SiteSetting::KEY_PARTNERS_PAGE_SUBTITLE,
            'partners_cta_title' => SiteSetting::KEY_PARTNERS_CTA_TITLE,
            'rating_title' => SiteSetting::KEY_RATING_PAGE_TITLE,
            'rating_subtitle' => SiteSetting::KEY_RATING_PAGE_SUBTITLE,
            'gallery_title' => SiteSetting::KEY_GALLERY_PAGE_TITLE,
        ];
        foreach ($map as $formKey => $settingKey) {
            if (array_key_exists($formKey, $pageText)) {
                SiteSetting::set($settingKey, $pageText[$formKey] ?? '');
            }
        }
    }

    // ─── Defaults ──────────────────────────────────────────────

    protected function defaultTitle(string $slug): string
    {
        return match ($slug) {
            SitePage::SLUG_ABOUT => 'О команде организатора',
            SitePage::SLUG_RULES => 'Правила забега',
            SitePage::SLUG_PRIVACY => 'Политика конфиденциальности',
            SitePage::SLUG_TERMS => 'Условия продажи мерча',
            SitePage::SLUG_CONSENT => 'Согласие на обработку данных',
            SitePage::SLUG_RETURNS => 'Возврат и обмен',
            SitePage::SLUG_TRAVEL => 'Где жить, как добраться до места старта',
            default => '',
        };
    }

    protected function defaultHomeStatNumber(int $i): string
    {
        return match ($i) { 1 => '12', 2 => '34', 3 => '300+', 4 => '600+', default => '' };
    }

    protected function defaultHomeStatLabel(int $i): string
    {
        return match ($i) {
            1 => 'оригинальных тематических забегов', 2 => 'увлекательных маршрутов',
            3 => 'ключевых локаций', 4 => 'интеллектуальных и активных заданий', default => '',
        };
    }

    protected function defaultHomeStatDesc(int $i): string
    {
        return match ($i) {
            1 => 'Получайте очки для победы в общем зачёте', 2 => 'Определяйте лучшую логистику для победы',
            3 => 'Узнавайте редкие места, погружайтесь в легендарные истории', 4 => 'Разгадывайте и узнавайте', default => '',
        };
    }

    /** @return array<int, array{title: string, content_type: string, links?: array, content?: string}> */
    protected function defaultInfoAccordionItems(): array
    {
        return [
            ['title' => 'Коротко о главном', 'content_type' => 'links', 'links' => [
                ['text' => 'Гонки', 'url' => '/events'], ['text' => 'Магазин', 'url' => '/shop'],
                ['text' => 'О нас', 'url' => '/about'], ['text' => 'Контакты', 'url' => '/contact'],
            ]],
            ['title' => 'Основные условия', 'content_type' => 'links', 'links' => [
                ['text' => 'Политика конфиденциальности', 'url' => '/privacy'], ['text' => 'Согласие на обработку ПДн', 'url' => '/consent'],
                ['text' => 'Условия продажи мерча', 'url' => '/terms'], ['text' => 'Правила возвратов', 'url' => '/returns'],
            ]],
            ['title' => 'Место старта, финиша, выдача номеров и стартовых пакетов', 'content_type' => 'prose',
             'content' => '<p>Старт и финиш каждой гонки указаны на странице конкретного события. Там же — время и место выдачи стартовых номеров и стартовых пакетов.</p><p>Актуальную информацию по каждой гонке смотрите в разделе <a href="/events">Гонки</a>. По вопросам организации обращайтесь в <a href="/contact">Контакты</a>.</p>'],
            ['title' => 'Где жить, как добраться до места старта', 'content_type' => 'prose',
             'content' => '<p>Рекомендации по проживанию и проезду до места старта — на отдельной странице.</p><a href="/travel" class="btn btn--info-inline">Где жить и как добраться →</a>'],
        ];
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
