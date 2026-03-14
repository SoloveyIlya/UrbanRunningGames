<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumResource\Pages;
use App\Models\Album;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Альбомы';

    protected static ?string $modelLabel = 'Альбом';

    protected static ?string $pluralModelLabel = 'Альбомы';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Альбом')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->label('Событие')
                            ->relationship(
                                'event',
                                'title',
                                fn (Builder $query) => $query->where('status', 'published')->orderBy('starts_at', 'desc')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image_upload')
                            ->label('Обложка')
                            ->image()
                            ->maxSize(50 * 1024)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->nullable()
                            ->storeFiles(false)
                            ->helperText('Загрузите изображение обложки альбома. Если не задана — используется первое фото.'),
                        Forms\Components\Placeholder::make('cover_preview')
                            ->label('Текущая обложка')
                            ->content(fn (?Album $record) => $record && $record->coverMedia
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($record->coverMedia->thumbnail_url ?? $record->coverMedia->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                                : 'Не задана')
                            ->visibleOn('edit'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Опубликован')
                            ->nullable()
                            ->native(false)
                            ->displayFormat('d.m.Y H:i')
                            ->helperText('Заполните, чтобы альбом отображался на сайте'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                ...(class_exists(SpatieMediaLibraryFileUpload::class) ? [
                    Forms\Components\Section::make('Фото альбома (медиа)')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('photos')
                                ->collection(Album::MEDIA_COLLECTION_PHOTOS)
                                ->label('Фото')
                                ->multiple()
                                ->maxFiles(100)
                                ->image()
                                ->maxSize(50 * 1024)
                                ->reorderable()
                                ->openable()
                                ->previewable()
                                ->imagePreviewHeight('150')
                                ->panelLayout('grid')
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),
                ] : []),
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
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Событие')
                    ->sortable(),
                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Фото')
                    ->getStateUsing(fn (Album $record) => $record->photos_count)
                    ->sortable(false),
                Tables\Columns\IconColumn::make('published_at')
                    ->label('Опубликован')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Событие')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
