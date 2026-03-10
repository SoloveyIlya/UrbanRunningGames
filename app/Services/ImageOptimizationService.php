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
        $fullPath = $file->getRealPath();
        $image = Image::read($fullPath);

        $mime = strtolower($file->getMimeType());
        $extOriginal = strtolower($file->getClientOriginalExtension());
        $preserveTransparency = in_array($mime, ['image/png', 'image/webp'], true)
            || in_array($extOriginal, ['png', 'webp'], true);

        if ($preserveTransparency) {
            return $this->processUploadAsPng($image, $fullPath, $disk, $directory, $safeName, $file->getClientOriginalName(), $createdByAdminId);
        }

        $ext = 'jpg';
        $uniqueId = uniqid('', true);
        $mainPath = $directory . '/' . $safeName . '-' . $uniqueId . '.' . $ext;
        $thumbPath = $directory . '/' . self::THUMB_DIR . '/' . $safeName . '-' . $uniqueId . '.' . $ext;

        $image->scaleDown(width: self::MAX_SIZE, height: self::MAX_SIZE);
        $mainFullPath = Storage::disk($disk)->path($mainPath);
        File::ensureDirectoryExists(dirname($mainFullPath));
        $image->toJpeg(quality: self::QUALITY)->save($mainFullPath);

        $sizeBytes = filesize($mainFullPath);
        $width = $image->width();
        $height = $image->height();

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
     * Сохранение изображения в PNG без потери прозрачности (для товаров без фона).
     */
    private function processUploadAsPng($image, string $fullPath, string $disk, string $directory, string $safeName, string $originalName, ?int $createdByAdminId = null): MediaAsset
    {
        $ext = 'png';
        $uniqueId = uniqid('', true);
        $mainPath = $directory . '/' . $safeName . '-' . $uniqueId . '.' . $ext;

        $image->scaleDown(width: self::MAX_SIZE, height: self::MAX_SIZE);
        $mainFullPath = Storage::disk($disk)->path($mainPath);
        File::ensureDirectoryExists(dirname($mainFullPath));
        $image->toPng()->save($mainFullPath);

        $sizeBytes = filesize($mainFullPath);
        $width = $image->width();
        $height = $image->height();

        $asset = new MediaAsset([
            'type' => 'image',
            'disk' => $disk,
            'path' => $mainPath,
            'thumbnail_path' => $mainPath,
            'mime_type' => 'image/png',
            'size_bytes' => $sizeBytes,
            'width' => $width,
            'height' => $height,
            'original_name' => $originalName,
            'created_by_admin_id' => $createdByAdminId,
        ]);
        $asset->save();

        return $asset;
    }

    /**
     * Обработка изображения по пути (например, временный файл из Filament).
     * SVG сохраняется как есть (без декодирования Intervention Image).
     */
    public function processUploadFromPath(string $path, ?string $originalName = null, ?int $createdByAdminId = null): MediaAsset
    {
        $originalName = $originalName ?? basename($path);
        $mime = mime_content_type($path);
        $isSvg = in_array(strtolower($mime), ['image/svg+xml', 'image/svg'], true)
            || (pathinfo($path, PATHINFO_EXTENSION) === 'svg');

        if ($isSvg) {
            return $this->storeFileAsIs($path, $originalName, $mime, $createdByAdminId);
        }

        $file = new UploadedFile($path, $originalName, $mime, 0, true);
        return $this->processUpload($file, $createdByAdminId);
    }

    /**
     * Сохранить файл как есть (SVG и др.) без декодирования через Intervention Image.
     */
    public function storeFileAsIs(string $path, string $originalName, string $mimeType, ?int $createdByAdminId = null): MediaAsset
    {
        $disk = 'public';
        $directory = 'gallery/' . date('Y/m/d');
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = \Illuminate\Support\Str::slug($baseName) ?: 'file';
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) ?: 'svg';
        $uniqueId = uniqid('', true);
        $mainPath = $directory . '/' . $safeName . '-' . $uniqueId . '.' . $ext;

        $mainFullPath = Storage::disk($disk)->path($mainPath);
        File::ensureDirectoryExists(dirname($mainFullPath));
        copy($path, $mainFullPath);

        $sizeBytes = filesize($mainFullPath);

        $asset = new MediaAsset([
            'type' => 'image',
            'disk' => $disk,
            'path' => $mainPath,
            'thumbnail_path' => $mainPath,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'width' => null,
            'height' => null,
            'original_name' => $originalName,
            'created_by_admin_id' => $createdByAdminId,
        ]);
        $asset->save();

        return $asset;
    }
}

