<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Mail\IncidentReported;
use App\Models\Incident;
use App\Models\IncidentImage;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IncidentController extends Controller {
    function store(StoreIncidentRequest $request) {
        $validated = $request->validated();

        $reservation = Reservation::findOrFail($validated['reservation_id']);

        // Verificar ownership el wp_user_id debe coincidir con el de la reserva
        if ((int) $reservation->wp_user_id !== (int) $validated['wp_user_id']) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para reportar incidencias en esta reserva.',
            ], 403);
        }

        // Crear la incidencia con car_id derivado de la reserva
        $incident = Incident::create([
            'reservation_id' => $reservation->id,
            'car_id'         => $reservation->car_id,
            'wp_user_id'     => $validated['wp_user_id'],
            'type'           => $validated['type'],
            'description'    => $validated['description'],
            'status'         => 'open',
        ]);

        // Guardar imágenes si existen
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('incidents', 'public');
                IncidentImage::create([
                    'incident_id' => $incident->id,
                    'path'        => $path,
                    'type'        => 'general',
                ]);
            }
        }

        // Notificar al admin por email
        try {
            $incident->load(['car', 'reservation']);
            Mail::to(config('mail.admin_address'))->send(
                new IncidentReported($incident)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send incident admin email: ' . $e->getMessage(), [
                'incident_id' => $incident->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Incidencia creada correctamente.',
            'data'    => $incident->load('images'),
        ], 201);
    }
}

