<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Состав заказа';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Товар'),
                Tables\Columns\TextColumn::make('variant_label')->label('Вариант'),
                Tables\Columns\TextColumn::make('quantity')->label('Кол-во'),
                Tables\Columns\TextColumn::make('price_amount')->label('Цена')->money('RUB'),
                Tables\Columns\TextColumn::make('subtotal')->label('Сумма'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
