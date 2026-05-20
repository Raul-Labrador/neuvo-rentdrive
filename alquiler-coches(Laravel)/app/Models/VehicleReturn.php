<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleReturn extends Model {
    protected $table = 'vehicle_returns';

    protected $fillable = [
        'reservation_id',
        'km_returned',
        'is_clean',
        'notes',
        'damages',
        'needs_review',
        'final_car_status',
        'returned_at',
    ];

    protected $casts = [
        'is_clean'       => 'boolean',
        'needs_review'   => 'boolean',
        'returned_at'    => 'datetime',
    ];

    function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class);
    }

    function images(): HasMany {
        return $this->hasMany(ReturnImage::class);
    }
}
