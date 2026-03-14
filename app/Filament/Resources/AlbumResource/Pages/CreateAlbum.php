<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Services\ImageOptimizationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAlbum extends CreateRecord
{
    protected static string $resource = AlbumResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $path = $data['cover_image_upload'] ?? null;
        if ($path && is_string($path)) {
            $fullPath = Storage::disk('local')->path($path);
            if (is_file($fullPath)) {
                $service = app(ImageOptimizationService::class);
                $asset = $service->processUploadFromPath($fullPath, null, auth()->id());
                $data['cover_media_id'] = $asset->id;
                @unlink($fullPath);
            }
        }
        unset($data['cover_image_upload']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Альбом создан. Добавьте фото на вкладке «Фото альбома».';
    }
}
