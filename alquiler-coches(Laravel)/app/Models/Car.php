<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model {
    function reservations(): HasMany {
        return $this->hasMany(Reservation::class);
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_per_day',
        'is_active',
        'brand',
        'model',
        'year',
        'price',
        'fuel',
        'km',
        'transmission',
        'engine_displacement',
        'horsepower',
        'emissions',
        'doors',
        'seats',
        'body',
        'trunk',
        'color',
        'features',
        'status',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'price_per_day' => 'decimal:2',
        'price'       => 'decimal:2',
        'year'        => 'integer',
        'km'          => 'integer',
        'horsepower'  => 'integer',
        'doors'       => 'integer',
        'seats'       => 'integer',
        'features'    => 'array',
    ];

    function incidents(): HasMany {
        return $this->hasMany(Incident::class);
    }
}