<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'adminVariants';

    protected static ?string $title = 'Варианты';

    protected static ?string $modelLabel = 'вариант';

    protected static ?string $pluralModelLabel = 'варианты';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('size')
                    ->label('Размер')
                    ->maxLength(16)
                    ->placeholder('S, M, L, XL…'),
                Forms\Components\Select::make('gender')
                    ->label('Пол')
                    ->options(\App\Models\ProductVariant::getGenderLabels())
                    ->nullable()
                    ->placeholder('Универсальный'),
                Forms\Components\TextInput::make('sku')
                    ->label('Артикул (SKU)')
                    ->maxLength(64)
                    ->unique(ignoreRecord: true)
                    ->nullable(),
                Forms\Components\TextInput::make('price_override')
                    ->label('Цена (переопределение, ₽)')
                    ->numeric()
                    ->minValue(0)
                    ->nullable()
                    ->helperText('Оставьте пустым, чтобы использовать цену товара'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Активен')
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attribute_label')
            ->columns([
                Tables\Columns\TextColumn::make('size')
                    ->label('Размер')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Пол')
                    ->formatStateUsing(fn ($state) => \App\Models\ProductVariant::getGenderLabels()[$state] ?? 'Универсальный'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('Артикул')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('price_override')
                    ->label('Цена')
                    ->money('RUB')
                    ->placeholder('Базовая'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить вариант'),
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
