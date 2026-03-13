<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Services\HeroVideoUploadService;
use App\Services\ImageOptimizationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Событие успешно сохранено';
    }
}
