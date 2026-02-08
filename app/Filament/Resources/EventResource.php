<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
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
                    ])
                    ->columns(2),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
