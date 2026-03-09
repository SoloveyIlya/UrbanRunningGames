<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                        Forms\Components\FileUpload::make('cover_image_upload')
                            ->label('Обложка (главное фото)')
                            ->image()
                            ->maxSize(50 * 1024)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->helperText('Загрузите одну картинку — как в альбомах. Отображается на карточке товара и в каталоге.'),
                        Forms\Components\Placeholder::make('cover_preview')
                            ->label('Текущая обложка')
                            ->content(fn (?Product $record) => $record && $record->coverMedia
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($record->coverMedia->thumbnail_url ?? $record->coverMedia->url) . '" alt="" style="max-width:200px;height:auto;border-radius:8px;">')
                                : 'Не задана')
                            ->visibleOn('edit'),
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
                Tables\Actions\ReplicateAction::make()
                    ->label('Копировать')
                    ->modalHeading('Копировать товар')
                    ->modalSubmitActionLabel('Копировать')
                    ->excludeAttributes(['id', 'variants_count'])
                    ->mutateRecordDataUsing(function (array $data, Product $record): array {
                        $data['name'] = $record->name . ' (копия)';
                        $data['cover_media_id'] = null;
                        return $data;
                    })
                    ->after(function (Product $replica, Product $record): void {
                        $sync = [];
                        foreach ($record->media()->orderByPivot('sort_order')->get() as $m) {
                            $sync[$m->id] = ['sort_order' => $m->pivot->sort_order];
                        }
                        if (! empty($sync)) {
                            $replica->media()->attach($sync);
                        }
                        $index = 0;
                        foreach ($record->adminVariants as $v) {
                            $base = $v->sku ?? '';
                            $newSku = $base !== ''
                                ? Str::limit($base, 48, '') . '-c' . $replica->id . '-' . (++$index)
                                : null;
                            $replica->adminVariants()->create([
                                'size' => $v->size,
                                'color' => $v->color,
                                'sku' => $newSku,
                                'price_override' => $v->price_override,
                                'is_active' => $v->is_active,
                            ]);
                        }
                    })
                    ->successRedirectUrl(fn (Product $replica) => ProductResource::getUrl('edit', ['record' => $replica])),
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
