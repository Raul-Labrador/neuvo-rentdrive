<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Services\AvailabilityService;
use App\Models\Car;

class AvailabilityController extends Controller {
    protected AvailabilityService $availabilityService;

    function __construct(AvailabilityService $availabilityService) {
        $this->availabilityService = $availabilityService;
    }

    function check(CheckAvailabilityRequest $request) {
        $validated = $request->validated();
        
        $isAvailable = $this->availabilityService->isCarAvailable(
            $validated['car_id'],
            $validated['start_date'],
            $validated['end_date']
        );

        if (!$isAvailable) {
            return response()->json([
                'available' => false,
                'message'   => 'Car not available for selected dates'
            ], 200);
        }

        $car = Car::select('id', 'price_per_day', 'status')
            ->findOrFail($validated['car_id']);

        if ($car->status !== 'available') {
            return response()->json([
                'available' => false,
                'message'   => 'Car is not available'
            ], 200);
        }
        
        $days = $this->availabilityService->calculateDays(
            $validated['start_date'],
            $validated['end_date']
        );

        $totalPrice = $days * $car->price_per_day;

        return response()->json([
            'available'   => true,
            'days'        => $days,
            'total_price' => number_format($totalPrice, 2, '.', ''),
            'currency'    => 'EUR'
        ], 200);
    }
}
