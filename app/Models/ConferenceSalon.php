<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceSalon extends Model
{
    protected $table = 'conference_salon';
    protected $fillable = [
        'conference_id',
        'salon_id',
        'visitor_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
