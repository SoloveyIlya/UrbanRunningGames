<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Album;
use App\Models\Event;
use App\Models\LevelTranslation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'События';

    protected static ?string $modelLabel = 'Событие';

    protected static ?string $pluralModelLabel = 'События';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->visibleOn('edit'),
                        Forms\Components\Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('location_text')
                            ->label('Место проведения')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Дата и время начала')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y H:i'),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'draft' => 'Черновик',
                                'published' => 'Опубликовано',
                                'closed' => 'Закрыто',
                                'archived' => 'Архив',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Select::make('level')
                            ->label('Уровень')
                            ->options(fn () => LevelTranslation::optionsForLocale('ru'))
                            ->searchable()
                            ->nullable()
                            ->helperText('Отображается на странице гонки под заголовком. Переводы в таблице level_translations.'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Hero страницы гонки')
                    ->description('Видео, орнамент и прозрачность для hero на странице этой гонки. Если не заданы — используется только обложка гонки.')
                    ->schema([
                        Forms\Components\FileUpload::make('hero_video_upload')
                            ->label('Видео hero')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(100 * 1024 * 1024)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->helperText('MP4 или WebM. Если задано — в hero показывается видео.'),
                        Forms\Components\FileUpload::make('hero_ornament_upload')
                            ->label('Орнамент hero')
                            ->image()
                            ->maxSize(10 * 1024)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->helperText('Изображение (SVG/PNG) поверх hero.'),
                        Forms\Components\TextInput::make('hero_ornament_opacity')
                            ->label('Прозрачность орнамента')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.05)
                            ->default(0.85)
                            ->helperText('0—1, например 0.85'),
                        Forms\Components\Placeholder::make('hero_video_preview')
                            ->label('Текущее видео')
                            ->content(fn (?Event $record) => $record && $record->heroVideoMedia ? 'Задано' : 'Не задано')
                            ->visibleOn('edit'),
                        Forms\Components\Placeholder::make('hero_ornament_preview')
                            ->label('Текущий орнамент')
                            ->content(fn (?Event $record) => $record && $record->heroOrnamentMedia
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($record->heroOrnamentMedia->url) . '" alt="" style="max-width:120px;height:auto;">')
                                : 'Не задан')
                            ->visibleOn('edit'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make('Карточка на главной')
                    ->description('Главная картинка и параметры для блока «Ближайшие гонки».')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image_upload')
                            ->label('Главная картинка')
                            ->image()
                            ->maxSize(50 * 1024)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->helperText('Загрузите одну картинку — как в альбомах. Отображается на карточке события на главной.'),
                        Forms\Components\Placeholder::make('cover_preview')
                            ->label('Текущая обложка')
                            ->content(fn (?Event $record) => $record && $record->coverMedia
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($record->coverMedia->thumbnail_url ?? $record->coverMedia->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                                : 'Не задана')
                            ->visibleOn('edit'),
                        Forms\Components\TextInput::make('distance')
                            ->label('Расстояние')
                            ->maxLength(64)
                            ->placeholder('например: 19 км')
                            ->nullable(),
                        Forms\Components\TextInput::make('locations_count')
                            ->label('Локаций')
                            ->maxLength(64)
                            ->placeholder('например: 17')
                            ->nullable(),
                        Forms\Components\TextInput::make('time_limit')
                            ->label('Лимит')
                            ->maxLength(64)
                            ->placeholder('например: 3 часа')
                            ->nullable(),
                        Forms\Components\TextInput::make('teams_count')
                            ->label('Команд')
                            ->maxLength(64)
                            ->placeholder('например: 40')
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make('Описание и правила')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(5)
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rules')
                            ->label('Правила')
                            ->rows(4)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Альбомы (при создании)')
                    ->description('Добавьте один или несколько альбомов к событию. На странице редактирования альбомы управляются во вкладке ниже.')
                    ->schema([
                        Forms\Components\Repeater::make('new_albums')
                            ->label('Добавить альбомы')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Название альбома')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Описание')
                                    ->rows(2)
                                    ->nullable(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Опубликован')
                                    ->nullable()
                                    ->native(false)
                                    ->displayFormat('d.m.Y H:i')
                                    ->helperText('Оставьте пустым, чтобы альбом не показывался на сайте'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Порядок')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Число для сортировки: меньшие значения показываются первыми (0, 1, 2…)'),
                                Forms\Components\FileUpload::make('photos')
                                    ->label('Фото альбома')
                                    ->multiple()
                                    ->image()
                                    ->maxFiles(100)
                                    ->maxSize(50 * 1024)
                                    ->disk('local')
                                    ->directory('livewire-tmp')
                                    ->visibility('private')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Добавить альбом')
                            ->visible(fn ($livewire) => $livewire instanceof \App\Filament\Resources\EventResource\Pages\CreateEvent),
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof \App\Filament\Resources\EventResource\Pages\CreateEvent)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Город')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Дата начала')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Опубликовано',
                        'closed' => 'Закрыто',
                        'archived' => 'Архив',
                        default => 'Черновик',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'closed' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'draft' => 'Черновик',
                        'published' => 'Опубликовано',
                        'closed' => 'Закрыто',
                        'archived' => 'Архив',
                    ]),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->label('Копировать')
                    ->modalHeading('Копировать событие')
                    ->modalSubmitActionLabel('Копировать')
                    ->excludeAttributes(['id', 'slug'])
                    ->mutateRecordDataUsing(function (array $data, Event $record): array {
                        $data['title'] = $record->title . ' (копия)';
                        $data['slug'] = Event::uniqueSlug(\Illuminate\Support\Str::slug($data['title']), null);
                        $data['status'] = 'draft';
                        return $data;
                    })
                    ->beforeReplicaSaved(function (Event $replica): void {
                        $replica->slug = Event::uniqueSlug(
                            \Illuminate\Support\Str::slug($replica->title),
                            null
                        );
                    })
                    ->successRedirectUrl(fn (Event $replica) => EventResource::getUrl('edit', ['record' => $replica])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EventResource\RelationManagers\AlbumsRelationManager::class,
            EventResource\RelationManagers\DistancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
