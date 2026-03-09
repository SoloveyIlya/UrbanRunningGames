<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
    ];

    /** Slugs for editable pages (URL path or identifier). */
    public const SLUG_ABOUT = 'about';
    public const SLUG_RULES = 'rules';
    public const SLUG_PRIVACY = 'privacy';
    public const SLUG_TERMS = 'terms';
    public const SLUG_CONSENT = 'consent';
    public const SLUG_RETURNS = 'returns';
    public const SLUG_TRAVEL = 'travel';

    public static function slugs(): array
    {
        return [
            self::SLUG_HOME_INFO,
            self::SLUG_ABOUT,
            self::SLUG_RULES,
            self::SLUG_PRIVACY,
            self::SLUG_TERMS,
            self::SLUG_CONSENT,
            self::SLUG_RETURNS,
            self::SLUG_TRAVEL,
        ];
    }

    public static function getBySlug(string $slug): ?self
    {
        return static::query()->where('slug', $slug)->first();
    }

    public static function getContent(string $slug): ?string
    {
        $page = static::getBySlug($slug);

        return $page?->content;
    }
}
