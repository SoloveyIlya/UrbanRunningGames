<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Действие')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Дата и время')
                            ->dateTime('d.m.Y H:i:s'),
                        TextEntry::make('action')
                            ->label('Действие')
                            ->formatStateUsing(fn ($record) => $record->action_label),
                        TextEntry::make('entity_type')
                            ->label('Сущность')
                            ->formatStateUsing(fn ($record) => $record->entity_label),
                        TextEntry::make('admin.name')
                            ->label('Администратор')
                            ->placeholder('—'),
                        TextEntry::make('ip')->label('IP'),
                        TextEntry::make('user_agent')->label('User-Agent')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Было (old)')
                    ->schema([
                        KeyValueEntry::make('old')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->old)),
                Section::make('Стало (new)')
                    ->schema([
                        KeyValueEntry::make('new')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->new)),
            ]);
    }
}
