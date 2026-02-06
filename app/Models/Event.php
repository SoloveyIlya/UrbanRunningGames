<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'city_id',
        'location_text',
        'starts_at',
        'description',
        'rules',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'event_partners');
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at > now();
    }

    public function isPast(): bool
    {
        return $this->starts_at <= now();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'published' => $this->isUpcoming() ? 'Предстоящее' : 'Завершено',
            'closed' => 'Закрыто',
            'archived' => 'Архив',
            default => 'Черновик',
        };
    }
}
