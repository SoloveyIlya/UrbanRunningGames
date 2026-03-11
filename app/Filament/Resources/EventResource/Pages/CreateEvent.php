<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Album;
use App\Services\HeroVideoUploadService;
use App\Services\ImageOptimizationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

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

        $videoPath = $data['hero_video_upload'] ?? null;
        if ($videoPath && is_string($videoPath)) {
            $fullPath = Storage::disk('local')->path($videoPath);
            if (is_file($fullPath)) {
                $file = new UploadedFile($fullPath, basename($videoPath), mime_content_type($fullPath), 0, true);
                $asset = app(HeroVideoUploadService::class)->storeVideo($file, auth()->id());
                $data['hero_video_media_id'] = $asset->id;
                @unlink($fullPath);
            }
        }
        unset($data['hero_video_upload']);

        $ornamentPath = $data['hero_ornament_upload'] ?? null;
        if ($ornamentPath && is_string($ornamentPath)) {
            $fullPath = Storage::disk('local')->path($ornamentPath);
            if (is_file($fullPath)) {
                $asset = app(ImageOptimizationService::class)->processUploadFromPath($fullPath, null, auth()->id());
                $data['hero_ornament_media_id'] = $asset->id;
                @unlink($fullPath);
            }
        }
        unset($data['hero_ornament_upload']);

        return $data;
    }

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
