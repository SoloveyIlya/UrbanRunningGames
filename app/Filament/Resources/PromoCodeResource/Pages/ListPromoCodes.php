<?php

namespace App\Filament\Resources\PromoCodeResource\Pages;

use App\Filament\Resources\PromoCodeResource;
use App\Filament\Widgets\PromoCodeStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromoCodes extends ListRecords
{
    protected static string $resource = PromoCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Добавить промокод'),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [PromoCodeStatsWidget::class];
    }
}
