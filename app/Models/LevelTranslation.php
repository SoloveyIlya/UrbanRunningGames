<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelTranslation extends Model
{
    protected $fillable = ['level_key', 'label_en', 'label_ru'];

    public $timestamps = true;

    /**
     * Подпись уровня для локали (en или ru).
     */
    public static function labelFor(string $levelKey, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $row = static::where('level_key', $levelKey)->first();
        if (!$row) {
            return null;
        }
        if ($locale === 'ru' && $row->label_ru) {
            return $row->label_ru;
        }
        if ($locale === 'en' && $row->label_en) {
            return $row->label_en;
        }
        return $row->label_en ?? $row->label_ru ?? $levelKey;
    }

    /**
     * Варианты уровней для Select в админке (ключ => подпись на русском или английском).
     */
    public static function optionsForLocale(string $locale = 'ru'): array
    {
        $labelCol = $locale === 'en' ? 'label_en' : 'label_ru';
        return static::orderBy('level_key')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->level_key => $row->{$labelCol} ?? $row->label_en ?? $row->label_ru ?? $row->level_key])
            ->all();
    }
}
