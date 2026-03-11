<?php

namespace App\Filament\Resources\LevelTranslationResource\Pages;

use App\Filament\Resources\LevelTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLevelTranslations extends ListRecords
{
    protected static string $resource = LevelTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
