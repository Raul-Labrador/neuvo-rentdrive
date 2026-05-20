<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller {
    function index() {
        return Car::where('is_active', true)->get();
    }

    function show($slug) {
        return Car::where('slug', $slug)
                  ->where('is_active', true)
                  ->firstOrFail();
    }

    function bookedDates($id) {
        $reservations = \App\Models\Reservation::where('car_id', $id)
            ->where('status', 'confirmed')
            ->orderBy('start_date', 'asc')
            ->get(['start_date', 'end_date']);

        $bookedDates = $reservations->map(function ($reservation) {
            return [
                'start_date' => $reservation->start_date->format('Y-m-d'),
                'end_date'   => $reservation->end_date->copy()->subDay()->format('Y-m-d'),
            ];
        });

        return response()->json($bookedDates);
    }
}