<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDistance extends Model
{
    protected $fillable = [
        'event_id',
        'sort_order',
        'title',
        'title_ru',
        'distance',
        'elevation_gain',
        'checkpoints_count',
        'time_limit',
        'teams_count',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
