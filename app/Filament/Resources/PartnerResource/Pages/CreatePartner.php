<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use App\Services\ImageOptimizationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    /** Путь к загруженному логотипу до сохранения (форма сбрасывает state после create). */
    protected ?string $pendingLogoPath = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Партнёр успешно добавлен';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $logo = $data['logo_upload'] ?? null;
        if (is_array($logo)) {
            $logo = array_values($logo)[0] ?? null;
        }
        if ($logo instanceof TemporaryUploadedFile && $logo->exists()) {
            $this->pendingLogoPath = $logo->getRealPath();
        } elseif (is_string($logo) && $logo !== '') {
            $fullPath = Storage::disk('local')->path($logo);
            if (is_file($fullPath)) {
                $this->pendingLogoPath = $fullPath;
            }
        }
        unset($data['logo_upload']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->processLogoUpload();
    }

    private function processLogoUpload(): void
    {
        $path = $this->pendingLogoPath;
        if (empty($path) || ! is_file($path)) {
            return;
        }
        try {
            $service = app(ImageOptimizationService::class);
            $asset = $service->processUploadFromPath($path, null, auth()->id());
            $this->record->update(['logo_media_id' => $asset->id]);
        } finally {
            if ($path && str_starts_with($path, storage_path('app'))) {
                @unlink($path);
            }
            $this->pendingLogoPath = null;
        }
    }
}
