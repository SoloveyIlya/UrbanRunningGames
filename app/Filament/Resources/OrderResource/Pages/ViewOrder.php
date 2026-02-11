<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Контактные данные')
                    ->schema([
                        TextEntry::make('name')->label('Имя'),
                        TextEntry::make('phone')->label('Телефон'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('comment')->label('Комментарий')->placeholder('—'),
                        TextEntry::make('status')
                            ->label('Статус')
                            ->formatStateUsing(fn (string $state) => \App\Models\Order::statusOptions()[$state] ?? $state),
                        TextEntry::make('created_at')->label('Дата заявки')->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
