<?php

namespace App\Filament\Resources\HeroVideoResource\Pages;

use App\Filament\Resources\HeroVideoResource;
use App\Services\HeroVideoUploadService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditHeroVideo extends EditRecord
{
    protected static string $resource = HeroVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->hidden(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Hero-видео сохранено';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $service = app(HeroVideoUploadService::class);
        $adminId = auth()->id();

        $video = $data['video_upload'] ?? null;
        if (is_array($video)) {
            $video = array_values($video)[0] ?? null;
        }
        if ($video instanceof TemporaryUploadedFile && $video->exists()) {
            try {
                $asset = $service->storeVideo($video, $adminId);
                $this->record->update(['video_media_id' => $asset->id]);
            } finally {
                $video->delete();
            }
        } elseif (is_string($video) && $video !== '') {
            $fullPath = Storage::disk('local')->path($video);
            if (is_file($fullPath)) {
                try {
                    $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                    $asset = $service->storeVideo($file, $adminId);
                    $this->record->update(['video_media_id' => $asset->id]);
                } finally {
                    @unlink($fullPath);
                }
            }
        }

        $poster = $data['poster_upload'] ?? null;
        if (is_array($poster)) {
            $poster = array_values($poster)[0] ?? null;
        }
        if ($poster instanceof TemporaryUploadedFile && $poster->exists()) {
            try {
                $asset = $service->storePoster($poster, $adminId);
                $this->record->update(['poster_media_id' => $asset->id]);
            } finally {
                $poster->delete();
            }
        } elseif (is_string($poster) && $poster !== '') {
            $fullPath = Storage::disk('local')->path($poster);
            if (is_file($fullPath)) {
                try {
                    $file = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), mime_content_type($fullPath), 0, true);
                    $asset = $service->storePoster($file, $adminId);
                    $this->record->update(['poster_media_id' => $asset->id]);
                } finally {
                    @unlink($fullPath);
                }
            }
        }

        unset($data['video_upload'], $data['poster_upload']);
        return $data;
    }
}
