<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Services\ImageOptimizationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
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
