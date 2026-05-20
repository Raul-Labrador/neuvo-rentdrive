<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentImage extends Model {
    protected $fillable = [
        'incident_id',
        'path',
        'type',
    ];

    function incident(): BelongsTo {
        return $this->belongsTo(Incident::class);
    }
}
