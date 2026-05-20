<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model {
    protected $fillable = [
        'car_id',
        'start_date',
        'end_date',
        'total_price',
        'customer_name',
        'customer_email',
        'wp_user_id',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }

    function vehicleReturn(): HasOne {
        return $this->hasOne(VehicleReturn::class);
    }

    function incidents(): HasMany {
        return $this->hasMany(Incident::class);
    }
}
