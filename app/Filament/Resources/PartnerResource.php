<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Партнёры';

    protected static ?string $modelLabel = 'Партнёр';

    protected static ?string $pluralModelLabel = 'Партнёры';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Партнёр')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website_url')
                            ->label('Сайт (URL)')
                            ->url()
                            ->maxLength(512)
                            ->nullable(),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('level')
                            ->label('Уровень')
                            ->options([
                                'partner' => 'Партнёр',
                                'sponsor' => 'Спонсор',
                            ])
                            ->nullable()
                            ->placeholder('Без уровня'),
                        Forms\Components\FileUpload::make('logo_upload')
                            ->label('Логотип')
                            ->image()
                            ->imagePreviewHeight(120)
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private')
                            ->nullable()
                            ->storeFiles(false)
                            ->helperText('Загрузите изображение. На странице редактирования отображается текущий логотип.'),
                        Forms\Components\Placeholder::make('current_logo')
                            ->label('Текущий логотип')
                            ->content(fn ($record) => $record && $record->logoMedia?->url
                                ? new \Illuminate\Support\HtmlString('<img src="' . e($record->logoMedia->url) . '" alt="" style="max-height:120px;">')
                                : '—')
                            ->visibleOn('edit'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок')
                            ->numeric()
                            ->default(0)
                            ->helperText('Меньшее число — выше в списке'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_media_id')
                    ->label('Логотип')
                    ->getStateUsing(fn (Partner $record) => $record->logoMedia?->url)
                    ->circular()
                    ->defaultImageUrl(fn () => 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><rect fill="#eee" width="40" height="40"/><text x="50%" y="50%" fill="#999" font-size="8" text-anchor="middle" dy=".3em">—</text></svg>')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Уровень')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'sponsor' => 'Спонсор',
                        'partner' => 'Партнёр',
                        default => '—',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->label('Уровень')
                    ->options([
                        'partner' => 'Партнёр',
                        'sponsor' => 'Спонсор',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активен'),
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
            PartnerResource\RelationManagers\EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
