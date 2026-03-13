<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingEntry extends Model
{
    protected $table = 'rating_entries';

    public const TEAM_TYPE_MEN = 'men';
    public const TEAM_TYPE_WOMEN = 'women';
    public const TEAM_TYPE_MIXED = 'mixed';

    protected $fillable = [
        'team_name',
        'team_type',
        'points',
        'events_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'events_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public static function getTeamTypeLabels(): array
    {
        return [
            self::TEAM_TYPE_MEN => 'Мужчины',
            self::TEAM_TYPE_WOMEN => 'Женщины',
            self::TEAM_TYPE_MIXED => 'Смешанные',
        ];
    }

    public function getTeamTypeLabelAttribute(): string
    {
        return self::getTeamTypeLabels()[$this->team_type] ?? '—';
    }
}
