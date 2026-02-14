<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use App\Services\ImageOptimizationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

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
        return 'Партнёр успешно сохранён';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $logo = $data['logo_upload'] ?? null;
        if (is_array($logo)) {
            $logo = array_values($logo)[0] ?? null;
        }
        if ($logo instanceof TemporaryUploadedFile && $logo->exists()) {
            $fullPath = $logo->getRealPath();
            try {
                $service = app(ImageOptimizationService::class);
                $asset = $service->processUploadFromPath($fullPath, null, auth()->id());
                $this->record->update(['logo_media_id' => $asset->id]);
            } finally {
                $logo->delete();
            }
        } elseif (is_string($logo) && $logo !== '') {
            $fullPath = Storage::disk('local')->path($logo);
            if (is_file($fullPath)) {
                try {
                    $service = app(ImageOptimizationService::class);
                    $asset = $service->processUploadFromPath($fullPath, null, auth()->id());
                    $this->record->update(['logo_media_id' => $asset->id]);
                } finally {
                    @unlink($fullPath);
                }
            }
        }
        unset($data['logo_upload']);
        return $data;
    }
}
