<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $table = 'salons';
    protected $guarded = false;

    public function conferences(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Conference::class, 'conference_salon')
            ->withPivot('token')
            ->withTimestamps();
    }

    public function conferenceAttendances()
    {
        return $this->hasMany(ConferenceSalon::class);
    }
}
