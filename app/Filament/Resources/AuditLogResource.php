<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Журнал действий';

    protected static ?string $modelLabel = 'Запись журнала';

    protected static ?string $pluralModelLabel = 'Журнал действий';

    protected static ?string $navigationGroup = 'Система';

    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('№')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Действие')
                    ->formatStateUsing(fn (AuditLog $record) => $record->action_label)
                    ->badge()
                    ->color(fn (AuditLog $record): string => match ($record->action) {
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Сущность')
                    ->formatStateUsing(fn (AuditLog $record) => $record->entity_label),
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Администратор')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Действие')
                    ->options([
                        'create' => 'Создание',
                        'update' => 'Изменение',
                        'delete' => 'Удаление',
                    ]),
                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Тип сущности')
                    ->options([
                        'events' => 'События',
                        'products' => 'Товары',
                        'orders' => 'Заявки',
                        'partners' => 'Партнёры',
                        'albums' => 'Альбомы',
                        'promo_codes' => 'Промокоды',
                        'rating_entries' => 'Рейтинг',
                        'cities' => 'Города',
                        'site_pages' => 'Страницы сайта',
                        'contact_messages' => 'Обращения',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
