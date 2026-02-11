<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Заявки';

    protected static ?string $modelLabel = 'Заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    protected static ?string $navigationGroup = 'Магазин';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Контактные данные')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Имя')->required()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->label('Телефон')->required()->maxLength(50),
                        Forms\Components\TextInput::make('email')->email()->label('Email')->required(),
                        Forms\Components\Textarea::make('comment')->label('Комментарий')->rows(3)->nullable()->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make('Промокод')
                    ->schema([
                        Forms\Components\TextInput::make('promoCode.code')
                            ->label('Промокод')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Скидка (₽)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible()
                    ->visible(fn ($record) => $record && $record->promo_code_id),
                Forms\Components\Section::make('Статус')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Статус заявки')
                            ->options(Order::statusOptions())
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('№')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Имя')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Телефон')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Order::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'confirmed', 'in_progress' => 'warning',
                        'shipped', 'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('promoCode.code')
                    ->label('Промокод')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Позиций')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(Order::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email'];
    }
}
