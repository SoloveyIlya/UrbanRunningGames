<?php

namespace App\Services;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class HeroVideoUploadService
{
    private const HERO_VIDEO_DIR = 'hero/videos';
    private const HERO_POSTER_DIR = 'hero/posters';

    /**
     * Сохранить видео (mp4/webm), создать MediaAsset.
     */
    public function storeVideo(UploadedFile|TemporaryUploadedFile $file, ?int $createdByAdminId = null): MediaAsset
    {
        $disk = 'public';
        $directory = self::HERO_VIDEO_DIR . '/' . date('Y/m/d');
        $safeName = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'video';
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'mp4';
        $path = $directory . '/' . $safeName . '-' . uniqid('', true) . '.' . $ext;

        $fullPath = $file->getRealPath();
        $targetPath = Storage::disk($disk)->path($path);
        File::ensureDirectoryExists(dirname($targetPath));
        File::copy($fullPath, $targetPath);

        $mimeType = $file->getMimeType() ?: 'video/mp4';
        $sizeBytes = filesize($targetPath);

        $asset = new MediaAsset([
            'type' => 'video',
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'original_name' => $file->getClientOriginalName(),
            'created_by_admin_id' => $createdByAdminId,
        ]);
        $asset->save();

        return $asset;
    }

    /**
     * Сохранить постер (изображение), создать MediaAsset с превью.
     */
    public function storePoster(UploadedFile|TemporaryUploadedFile $file, ?int $createdByAdminId = null): MediaAsset
    {
        $path = $file->getRealPath();
        return app(ImageOptimizationService::class)->processUploadFromPath(
            $path,
            $file->getClientOriginalName(),
            $createdByAdminId
        );
    }
}
