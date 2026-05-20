<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller {
    function index(\App\Services\WordPressService $wpService): View {
        $totalCars = Car::count();
        $totalReservations = Reservation::count();
        $totalClients = $wpService->getSubscriberCount();
        
        $recentCars = Car::latest()->take(5)->get();

        $resolvedIncidents = \App\Models\Incident::where('status', 'resolved')->count();

        return view('dashboard', compact(
            'totalCars',
            'totalReservations',
            'totalClients',
            'recentCars',
            'resolvedIncidents'
        ));
    }
}
