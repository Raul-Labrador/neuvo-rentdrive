<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model {
    protected $fillable = [
        'reservation_id',
        'car_id',
        'wp_user_id',
        'type',
        'description',
        'status',
        'admin_notes',
    ];

    function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class);
    }

    function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }

    function images(): HasMany {
        return $this->hasMany(IncidentImage::class);
    }
}
