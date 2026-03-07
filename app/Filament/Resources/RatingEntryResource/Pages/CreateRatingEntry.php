<?php

namespace App\Filament\Resources\RatingEntryResource\Pages;

use App\Filament\Resources\RatingEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRatingEntry extends CreateRecord
{
    protected static string $resource = RatingEntryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Строка рейтинга добавлена';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! isset($data['sort_order']) || $data['sort_order'] === '' || $data['sort_order'] === null) {
            $max = (int) \App\Models\RatingEntry::query()->max('sort_order');
            $data['sort_order'] = $max + 1;
        }
        return $data;
    }
}
