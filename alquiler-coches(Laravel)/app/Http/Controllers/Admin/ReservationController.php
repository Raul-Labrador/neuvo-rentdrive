<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller {
    function index(Request $request): View {
        $currentFilter = $request->query('status', '');

        $query = Reservation::with('car', 'vehicleReturn')->latest();

        if ($currentFilter !== '') {
            $query->where('status', $currentFilter);
        }

        $reservations = $query->get();

        return view('admin.reservations.index', compact('reservations', 'currentFilter'));
    }
}
