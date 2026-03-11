<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LevelTranslationResource\Pages;
use App\Models\LevelTranslation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LevelTranslationResource extends Resource
{
    protected static ?string $model = LevelTranslation::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Уровни гонок';

    protected static ?string $modelLabel = 'уровень';

    protected static ?string $pluralModelLabel = 'Уровни';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'label_en';

    public static function getModelLabel(): string
    {
        return 'Уровень';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Уровни гонок';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Уровень')
                    ->schema([
                        Forms\Components\TextInput::make('level_key')
                            ->label('Ключ')
                            ->required()
                            ->maxLength(64)
                            ->placeholder('например: times-and-epochs')
                            ->helperText('Латинский ключ без пробелов.')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('label_en')
                            ->label('Название (англ.)')
                            ->maxLength(255)
                            ->placeholder('например: Times and Epochs'),
                        Forms\Components\TextInput::make('label_ru')
                            ->label('Название (рус.)')
                            ->maxLength(255)
                            ->placeholder('например: Времена и Эпохи'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level_key')
                    ->label('Ключ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label_en')
                    ->label('Название (англ.)')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('label_ru')
                    ->label('Название (рус.)')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('level_key')
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
            'index' => Pages\ListLevelTranslations::route('/'),
            'create' => Pages\CreateLevelTranslation::route('/create'),
            'edit' => Pages\EditLevelTranslation::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['level_key', 'label_en', 'label_ru'];
    }
}
