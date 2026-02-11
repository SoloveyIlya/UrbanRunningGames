<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Промокоды';

    protected static ?string $modelLabel = 'Промокод';

    protected static ?string $pluralModelLabel = 'Промокоды';

    protected static ?string $navigationGroup = 'Магазин';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основные настройки')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Код')
                            ->required()
                            ->maxLength(64)
                            ->unique(ignoreRecord: true)
                            ->placeholder('SUMMER20'),
                        Forms\Components\Select::make('type')
                            ->label('Тип скидки')
                            ->options(PromoCode::typeOptions())
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('value')
                            ->label('Значение')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Процент (1–100) или сумма в ₽'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Ограничения')
                    ->schema([
                        Forms\Components\DateTimePicker::make('valid_from')
                            ->label('Действует с')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('valid_until')
                            ->label('Действует до')
                            ->nullable(),
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Лимит использований')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->helperText('Пусто = без лимита'),
                        Forms\Components\TextInput::make('min_order_amount')
                            ->label('Минимальная сумма заказа (₽)')
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),
                        Forms\Components\Select::make('products')
                            ->label('Только для товаров')
                            ->relationship(
                                'products',
                                'name',
                                fn (Builder $query) => $query->orderBy('name')
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->nullable()
                            ->helperText('Пусто = ко всей корзине'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make('Статистика')
                    ->schema([
                        Forms\Components\TextInput::make('times_used')
                            ->label('Использовано раз')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->visible(fn (?PromoCode $record) => $record?->exists)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state) => PromoCode::typeOptions()[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Значение')
                    ->formatStateUsing(fn (PromoCode $record) => $record->type === PromoCode::TYPE_PERCENT ? $record->value . '%' : $record->value . ' ₽'),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label('С')
                    ->dateTime('d.m.Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('До')
                    ->dateTime('d.m.Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('times_used')
                    ->label('Использовано')
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Лимит')
                    ->placeholder('∞')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активен')
                    ->placeholder('Все')
                    ->trueLabel('Да')
                    ->falseLabel('Нет'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
