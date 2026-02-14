<?php

namespace App\Filament\Resources\RatingEntryResource\Pages;

use App\Filament\Resources\RatingEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRatingEntry extends EditRecord
{
    protected static string $resource = RatingEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Строка рейтинга сохранена';
    }
}
