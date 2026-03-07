<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Экспорт CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('admin.orders.export.csv'))
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
