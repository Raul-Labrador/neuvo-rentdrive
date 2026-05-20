<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class AvailabilityService {
    function isCarAvailable(int $carId, string $startDate, string $endDate): bool {
        return !Reservation::where('car_id', $carId)
            ->where('status', 'confirmed')
            ->where('start_date', '<', $endDate)
            ->where('end_date', '>', $startDate)
            ->exists();
    }

    function calculateDays(string $startDate, string $endDate): int {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->startOfDay();
        
        $days = $start->diffInDays($end);
        
        return $days > 0 ? (int) $days : 1;
    }
}
