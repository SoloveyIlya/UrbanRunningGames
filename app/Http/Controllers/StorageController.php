<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageController extends Controller
{
    /**
     * Раздача файлов из public-диска (запасной вариант, если симлинк не работает).
     * Безопасно: только публичный диск, путь без "..".
     */
    public function show(Request $request): StreamedResponse
    {
        $uriPath = parse_url($request->getRequestUri(), PHP_URL_PATH);
        $uriPath = $uriPath ?: '';
        $uriPath = ltrim($uriPath, '/');

        if (! str_starts_with($uriPath, 'storage/')) {
            abort(404);
        }
        $path = substr($uriPath, 8); // убираем "storage/"
        $path = str_replace(['../', '..\\'], '', $path);
        $path = ltrim($path, '/');

        $exists = Storage::disk('public')->exists($path);

        if ($path === '' || ! $exists) {
            abort(404);
        }

        return Storage::disk('public')->response($path, null, [
            'Content-Type' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
        ]);
    }
}
