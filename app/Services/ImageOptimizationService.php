<?php

namespace App\Services;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageOptimizationService
{
    /** Максимальная сторона основного изображения (px). */
    public const MAX_SIZE = 1920;

    /** Качество JPEG (1–100). */
    public const QUALITY = 85;

    /** Сторона превью (квадрат, px). */
    public const THUMB_SIZE = 400;

    /** Поддиректория для превью в storage. */
    public const THUMB_DIR = 'thumbnails';

    public function processUpload(UploadedFile $file, ?int $createdByAdminId = null): MediaAsset
    {
        $disk = 'public';
        $directory = 'gallery/' . date('Y/m/d');
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = \Illuminate\Support\Str::slug($baseName) ?: 'image';
        $ext = 'jpg';
        $uniqueId = uniqid('', true);
        $mainPath = $directory . '/' . $safeName . '-' . $uniqueId . '.' . $ext;
        $thumbPath = $directory . '/' . self::THUMB_DIR . '/' . $safeName . '-' . $uniqueId . '.' . $ext;

        $fullPath = $file->getRealPath();
        $image = Image::read($fullPath);

        // Оптимизация: уменьшение по большей стороне с сохранением пропорций
        $image->scaleDown(width: self::MAX_SIZE, height: self::MAX_SIZE);
        $mainFullPath = Storage::disk($disk)->path($mainPath);
        File::ensureDirectoryExists(dirname($mainFullPath));
        $image->toJpeg(quality: self::QUALITY)->save($mainFullPath);

        $sizeBytes = filesize($mainFullPath);
        $width = $image->width();
        $height = $image->height();

        // Генерация превью (квадрат по центру) — тот же uniqueId, чтобы имена совпадали
        $thumbImage = Image::read($fullPath);
        $thumbImage->cover(self::THUMB_SIZE, self::THUMB_SIZE);
        $thumbFullPath = Storage::disk($disk)->path($thumbPath);
        File::ensureDirectoryExists(dirname($thumbFullPath));
        $thumbImage->toJpeg(quality: 80)->save($thumbFullPath);

        $asset = new MediaAsset([
            'type' => 'image',
            'disk' => $disk,
            'path' => $mainPath,
            'thumbnail_path' => $thumbPath,
            'mime_type' => 'image/jpeg',
            'size_bytes' => $sizeBytes,
            'width' => $width,
            'height' => $height,
            'original_name' => $file->getClientOriginalName(),
            'created_by_admin_id' => $createdByAdminId,
        ]);
        $asset->save();

        return $asset;
    }

    /**
     * Обработка изображения по пути (например, временный файл из Filament).
     */
    public function processUploadFromPath(string $path, ?string $originalName = null, ?int $createdByAdminId = null): MediaAsset
    {
        $originalName = $originalName ?? basename($path);
        $file = new UploadedFile($path, $originalName, mime_content_type($path), 0, true);
        return $this->processUpload($file, $createdByAdminId);
    }
}
