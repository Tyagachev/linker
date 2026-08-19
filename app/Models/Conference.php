<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    protected $table = 'conferences';
    protected $guarded = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'scheduled_date' => 'datetime',
            'deadline_at' => 'datetime',
        ];
    }
    /*protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('d.m.Y') : null
        );
    }*/

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->translatedFormat('d F Y');
    }
    public function getFormattedScheduledDateAttribute(): string
    {
        return $this->scheduled_date->translatedFormat('d F Y');
    }

    public function getFormattedDeadlineAtAttribute(): ?string
    {
        return $this->deadline_at
            ? $this->deadline_at->format('d.m.Y H:i')
            : null;
    }

    public function attendances()
    {
        return $this->hasMany(ConferenceSalon::class);
    }

    public function salons()
    {
        return $this->belongsToMany(Salon::class, 'conference_salon')
            ->withPivot('visitor_token')
            ->withTimestamps();
    }
}
