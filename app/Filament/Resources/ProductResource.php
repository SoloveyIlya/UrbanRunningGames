<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\MediaAsset;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Товары';

    protected static ?string $modelLabel = 'Товар';

    protected static ?string $pluralModelLabel = 'Товары';

    protected static ?string $navigationGroup = 'Магазин';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(5)
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price_amount')
                            ->label('Цена (₽)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\Select::make('currency')
                            ->label('Валюта')
                            ->options(['RUB' => 'RUB'])
                            ->default('RUB')
                            ->required(),
                        Forms\Components\Select::make('cover_media_id')
                            ->label('Обложка (главное фото)')
                            ->relationship(
                                'coverMedia',
                                'path',
                                fn (Builder $query) => $query->where('type', 'image')->orderBy('created_at', 'desc')
                            )
                            ->getOptionLabelFromRecordUsing(fn (MediaAsset $record) => $record->original_name ?: $record->path)
                            ->searchable(['path', 'original_name'])
                            ->preload()
                            ->nullable()
                            ->helperText('Опционально. Иначе будет использовано первое фото из галереи.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Показывать в каталоге')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_media.path')
                    ->label('Фото')
                    ->getStateUsing(fn (Product $record) => $record->coverMedia?->path)
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_amount')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Варианты')
                    ->counts('variants')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('В каталоге')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('В каталоге')
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\MediaRelationManager::class,
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
