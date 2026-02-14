<?php

namespace App\Filament\Resources\RatingEntryResource\Pages;

use App\Filament\Resources\RatingEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRatingEntries extends ListRecords
{
    protected static string $resource = RatingEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
