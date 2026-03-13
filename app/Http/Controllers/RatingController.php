<?php

namespace App\Http\Controllers;

use App\Models\RatingEntry;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RatingController extends Controller
{
    public function index()
    {
        $entries = RatingEntry::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('rating', compact('entries'));
    }

    /**
     * Скачивание рейтинга в формате CSV (открывается в Excel).
     */
    public function export(): StreamedResponse
    {
        $entries = RatingEntry::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $filename = 'reiting_komand_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($entries) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM для корректного открытия в Excel

            fputcsv($handle, ['Место', 'Название команды', 'Участники', 'Очки', 'Событий'], ';');

            foreach ($entries as $index => $entry) {
                fputcsv($handle, [
                    $index + 1,
                    $entry->team_name,
                    $entry->team_type_label,
                    $entry->points,
                    $entry->events_count,
                ], ';');
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
