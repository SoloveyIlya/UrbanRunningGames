<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroVideoResource\Pages;
use App\Models\HeroVideo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class HeroVideoResource extends Resource
{
    protected static ?string $model = HeroVideo::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationLabel = 'Hero-видео';

    protected static ?string $modelLabel = 'Hero-видео';

    protected static ?string $pluralModelLabel = 'Hero-видео';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 1;

    /** Скрыт из меню: настройки перенесены во вкладку «Hero-контент» на странице Контент сайта. */
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Видеоанимация в hero-блоке')
                    ->description('Главная и страница «События». mp4 (H.264), автовоспроизведение без звука, зацикливание.')
                    ->schema([
                        Forms\Components\Placeholder::make('page_label')
                            ->label('Страница')
                            ->content(fn (?HeroVideo $record) => $record ? $record->page_label : '—'),
                        Forms\Components\Toggle::make('is_enabled')
                            ->label('Включено')
                            ->default(true)
                            ->helperText('Показывать видео в hero-блоке'),
                        Forms\Components\FileUpload::make('video_upload')
                            ->label('Видео (mp4, желательно webm)')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(100 * 1024 * 1024)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->nullable()
                            ->storeFiles(false)
                            ->helperText('Загрузка / замена. Формат mp4 (H.264) обязателен.'),
                        Forms\Components\Placeholder::make('current_video')
                            ->label('Текущее видео')
                            ->content(fn (?HeroVideo $record) => $record && $record->videoMedia
                                ? new \Illuminate\Support\HtmlString('<a href="' . e($record->video_url) . '" target="_blank" rel="noopener">' . e($record->videoMedia->original_name ?? 'Видео') . '</a>')
                                : '—')
                            ->visibleOn('edit'),
                        Forms\Components\FileUpload::make('poster_upload')
                            ->label('Постер (fallback при невозможности autoplay)')
                            ->image()
                            ->imagePreviewHeight(120)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->nullable()
                            ->storeFiles(false),
                        Forms\Components\Placeholder::make('current_poster')
                            ->label('Текущий постер')
                            ->content(fn (?HeroVideo $record) => $record && $record->posterMedia?->url
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($record->poster_url) . '" alt="" style="max-height:120px;">')
                                : '—')
                            ->visibleOn('edit'),
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('Например: Urban Running Games'),
                        Forms\Components\TextInput::make('button_text')
                            ->label('Текст кнопки')
                            ->maxLength(64)
                            ->nullable()
                            ->placeholder('Предстоящие события'),
                        Forms\Components\TextInput::make('button_url')
                            ->label('URL кнопки')
                            ->maxLength(512)
                            ->nullable()
                            ->placeholder('/events')
                            ->helperText('Относительный путь (например /events) или полный URL'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->label('Страница')
                    ->formatStateUsing(fn (HeroVideo $record) => $record->page_label)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label('Включено')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('video_media_id')
                    ->label('Видео')
                    ->formatStateUsing(fn (?int $state, HeroVideo $record) => $record->videoMedia ? '✓' : '—'),
                Tables\Columns\TextColumn::make('poster_media_id')
                    ->label('Постер')
                    ->formatStateUsing(fn (?int $state, HeroVideo $record) => $record->posterMedia ? '✓' : '—'),
            ])
            ->defaultSort('id')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroVideos::route('/'),
            'edit' => Pages\EditHeroVideo::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
