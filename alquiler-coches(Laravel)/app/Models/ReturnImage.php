<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnImage extends Model {
    protected $table = 'return_images';

    protected $fillable = [
        'vehicle_return_id',
        'path',
        'type',
    ];

    function vehicleReturn(): BelongsTo {
        return $this->belongsTo(VehicleReturn::class);
    }
}
