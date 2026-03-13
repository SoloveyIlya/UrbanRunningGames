<?php

namespace App\Filament\Resources\LevelTranslationResource\Pages;

use App\Filament\Resources\LevelTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLevelTranslation extends EditRecord
{
    protected static string $resource = LevelTranslationResource::class;

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
        return 'Уровень успешно сохранён';
    }
}
