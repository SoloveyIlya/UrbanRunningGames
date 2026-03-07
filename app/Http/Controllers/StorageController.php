<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageController extends Controller
{
    private const PLACEHOLDER_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect fill="#eee" width="400" height="400"/><text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="14" text-anchor="middle" dy=".3em">Фото недоступно</text></svg>';

    /**
     * Раздача медиа по пути (URL: /media/gallery/...). Всегда обрабатывается Laravel — при отсутствии файла 200 + плейсхолдер.
     */
    public function showMedia(Request $request, string $path): StreamedResponse|Response
    {
        $path = urldecode($path);
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');
        foreach (explode('/', $path) as $segment) {
            if ($segment === '..') {
                return $this->placeholderResponse();
            }
        }
        return $this->servePublicPath($request, $path);
    }

    /**
     * Раздача файлов из public-диска (запасной вариант, если симлинк не работает).
     * Если файла нет — отдаём плейсхолдер (200), чтобы не было 404 в галерее.
     */
    public function show(Request $request): StreamedResponse|Response
    {
        $uriPath = parse_url($request->getRequestUri(), PHP_URL_PATH);
        $uriPath = $uriPath ?: '';
        $uriPath = ltrim($uriPath, '/');

        if (! str_starts_with($uriPath, 'storage/')) {
            $this->logStorage404($request, $uriPath, 'path_does_not_start_with_storage');
            return $this->placeholderResponse();
        }
        $path = substr($uriPath, 8);
        $path = urldecode($path);
        $path = str_replace('\\', '/', $path);
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                $this->logStorage404($request, $path, 'path_traversal_rejected');
                return $this->placeholderResponse();
            }
        }
        $path = ltrim($path, '/');

        return $this->servePublicPath($request, $path);
    }

    private function servePublicPath(Request $request, string $path): StreamedResponse|Response
    {
        $disk = Storage::disk('public');
        $exists = $disk->exists($path);
        $fullPath = $disk->path($path);

        if ($path !== '' && ! $exists && str_contains($path, '/thumbnails/')) {
            $fallbackPath = preg_replace('#/thumbnails/#', '/', $path, 1);
            if ($fallbackPath !== $path && $disk->exists($fallbackPath)) {
                return $disk->response($fallbackPath, null, [
                    'Content-Type' => $disk->mimeType($fallbackPath) ?: 'application/octet-stream',
                ]);
            }
        }

        if ($path === '' || ! $exists) {
            $this->logStorage404($request, $path, 'file_not_found', [
                'resolved_full_path' => $fullPath,
                'file_exists' => $exists,
                'parent_dir_exists' => $path !== '' ? $disk->exists(dirname($path)) : false,
            ]);
            return $this->placeholderResponse();
        }

        return $disk->response($path, null, [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
        ]);
    }

    private function placeholderResponse(): Response
    {
        return response(self::PLACEHOLDER_SVG, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=60',
        ]);
    }

    private function logStorage404(Request $request, string $path, string $reason, array $context = []): void
    {
        Log::channel('single')->warning('[Storage 404] ' . $reason, array_merge($context, [
            'uri' => $request->getRequestUri(),
            'path' => $path,
            'ip' => $request->ip(),
        ]));
    }
}
