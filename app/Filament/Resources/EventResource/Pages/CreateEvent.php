<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Album;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Событие успешно создано';
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        $items = $data['new_albums'] ?? [];
        if (empty($items) || ! is_array($items)) {
            return;
        }
        foreach ($items as $item) {
            if (empty($item['title'] ?? null)) {
                continue;
            }
            $album = $this->record->albums()->create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'published_at' => $item['published_at'] ?? null,
                'sort_order' => (int) ($item['sort_order'] ?? 0),
            ]);

            $photos = $item['photos'] ?? [];
            if (is_array($photos)) {
                foreach ($photos as $path) {
                    if (empty($path)) {
                        continue;
                    }
                    $fullPath = Storage::disk('local')->path($path);
                    if (is_file($fullPath)) {
                        try {
                            $album->addMedia($fullPath)->toMediaCollection(Album::MEDIA_COLLECTION_PHOTOS);
                        } finally {
                            @unlink($fullPath);
                        }
                    }
                }
            }
        }
    }
}
