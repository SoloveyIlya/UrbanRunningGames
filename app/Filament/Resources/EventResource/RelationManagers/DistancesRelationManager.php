<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DistancesRelationManager extends RelationManager
{
    protected static string $relationship = 'distances';

    protected static ?string $title = 'Дистанции';

    protected static ?string $modelLabel = 'дистанция';

    protected static ?string $pluralModelLabel = 'дистанции';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название дистанции (англ.)')
                    ->placeholder('например: Rush 20K')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('title_ru')
                    ->label('Название дистанции (рус.)')
                    ->placeholder('например: Порыв 20K')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('distance')
                    ->label('Расстояние')
                    ->placeholder('например: 19 км')
                    ->maxLength(64)
                    ->nullable(),
                Forms\Components\TextInput::make('elevation_gain')
                    ->label('Набор высоты')
                    ->placeholder('например: 100 м')
                    ->maxLength(64)
                    ->nullable(),
                Forms\Components\TextInput::make('checkpoints_count')
                    ->label('Чекпоинты с заданиями')
                    ->placeholder('например: 17 шт')
                    ->maxLength(64)
                    ->nullable(),
                Forms\Components\TextInput::make('time_limit')
                    ->label('Лимит времени')
                    ->placeholder('например: 3 часа')
                    ->maxLength(64)
                    ->nullable(),
                Forms\Components\TextInput::make('teams_count')
                    ->label('Лимит команд')
                    ->placeholder('например: 40 ед')
                    ->maxLength(64)
                    ->nullable(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->helperText('Меньшее число — выше в списке (0, 1, 2…).'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название (англ.)')
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title_ru')
                    ->label('Название (рус.)')
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('distance')
                    ->label('Расстояние')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('elevation_gain')
                    ->label('Набор высоты')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('checkpoints_count')
                    ->label('Чекпоинты')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('time_limit')
                    ->label('Лимит времени')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('teams_count')
                    ->label('Лимит команд')
                    ->placeholder('—'),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
