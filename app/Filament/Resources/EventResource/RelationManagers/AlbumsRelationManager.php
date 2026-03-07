<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AlbumsRelationManager extends RelationManager
{
    protected static string $relationship = 'albums';

    protected static ?string $title = 'Альбомы';

    protected static ?string $modelLabel = 'альбом';

    protected static ?string $pluralModelLabel = 'альбомы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Опубликован')
                    ->nullable()
                    ->native(false)
                    ->displayFormat('d.m.Y H:i'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->helperText('Меньшее число — выше в списке альбомов (0, 1, 2…).'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\IconColumn::make('published_at')
                    ->label('Опубликован')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
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
