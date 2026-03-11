<?php

namespace App\Filament\Resources\LevelTranslationResource\Pages;

use App\Filament\Resources\LevelTranslationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLevelTranslation extends CreateRecord
{
    protected static string $resource = LevelTranslationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Уровень успешно добавлен';
    }
}
