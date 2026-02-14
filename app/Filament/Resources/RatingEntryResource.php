<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingEntryResource\Pages;
use App\Models\RatingEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RatingEntryResource extends Resource
{
    protected static ?string $model = RatingEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Рейтинг';

    protected static ?string $modelLabel = 'Строка рейтинга';

    protected static ?string $pluralModelLabel = 'Рейтинг';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Команда в рейтинге')
                    ->schema([
                        Forms\Components\TextInput::make('team_name')
                            ->label('Название команды')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('points')
                            ->label('Очки')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('events_count')
                            ->label('Количество событий')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок (место)')
                            ->numeric()
                            ->default(0)
                            ->helperText('Меньшее число — выше в таблице (1, 2, 3…).'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Место')
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_name')
                    ->label('Команда')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Очки')
                    ->sortable(),
                Tables\Columns\TextColumn::make('events_count')
                    ->label('Событий')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatingEntries::route('/'),
            'create' => Pages\CreateRatingEntry::route('/create'),
            'edit' => Pages\EditRatingEntry::route('/{record}/edit'),
        ];
    }
}
