<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\VehicleReturn;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReturnController extends Controller {
    function create(Request $request): View {
        $reservation = Reservation::with('car')
            ->findOrFail($request->query('reservation_id'));

        return view('admin.returns.create', compact('reservation'));
    }

    function show($reservationId): View {
        $vehicleReturn = VehicleReturn::with('reservation.car')
            ->where('reservation_id', $reservationId)
            ->first();

        if (!$vehicleReturn) {
            return redirect()->route('admin.reservations.index')
                ->with('error', 'No inspection found for this reservation.');
        }

        return view('admin.returns.show', compact('vehicleReturn'));
    }

    function store(Request $request): RedirectResponse {
        // Validación
        $validated = $request->validate([
            'reservation_id'   => 'required|integer|exists:reservations,id',
            'km_returned'      => 'required|integer|min:0',
            'is_clean'         => 'required|boolean',
            'notes'            => 'nullable|string|max:2000',
            'damages'          => 'nullable|string|max:2000',
            'needs_review'     => 'required|boolean',
            'final_car_status' => 'required|string|in:available,maintenance,unavailable',
        ]);

        // Buscar reserva con coche
        $reservation = Reservation::with('car')->findOrFail($validated['reservation_id']);

        // Crear o actualizar devolución (evita duplicados)
        $vehicleReturn = VehicleReturn::updateOrCreate(
            ['reservation_id' => $validated['reservation_id']],
            [
                'km_returned'      => $validated['km_returned'],
                'is_clean'         => $validated['is_clean'],
                'notes'            => $validated['notes'] ?? null,
                'damages'          => $validated['damages'] ?? null,
                'needs_review'     => $validated['needs_review'],
                'final_car_status' => $validated['final_car_status'],
                'returned_at'      => now(),
            ]
        );

        // Marcar reserva como completada
        $reservation->status = 'completed';
        $reservation->save();

        // Actualizar estado del coche
        $reservation->car->status = $validated['final_car_status'];
        $reservation->car->save();

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Devolución registrada correctamente.');
    }
}
