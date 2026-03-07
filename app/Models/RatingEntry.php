<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingEntry extends Model
{
    protected $table = 'rating_entries';

    protected $fillable = [
        'team_name',
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
}
